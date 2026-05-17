import json
import re
import sys
from collections import Counter

import nltk
import spacy
from nltk.corpus import stopwords
from nltk.sentiment import SentimentIntensityAnalyzer
from nltk.tokenize import word_tokenize


POSITIVE_WORDS = {
    "amazing",
    "awesome",
    "best",
    "excellent",
    "fantastic",
    "good",
    "great",
    "happy",
    "helpful",
    "impressive",
    "like",
    "love",
    "nice",
    "perfect",
    "thanks",
    "wonderful",
}

NEGATIVE_WORDS = {
    "angry",
    "awful",
    "bad",
    "broken",
    "confused",
    "disappointed",
    "hate",
    "issue",
    "problem",
    "sad",
    "terrible",
    "upset",
    "worst",
}

CLASSIFICATION_RULES = {
    "Complaint": {
        "bad",
        "broken",
        "complain",
        "error",
        "hate",
        "issue",
        "problem",
        "slow",
        "terrible",
        "wrong",
    },
    "Inquiry": {
        "can",
        "could",
        "explain",
        "how",
        "what",
        "when",
        "where",
        "which",
        "who",
        "why",
    },
    "Feedback": {
        "awesome",
        "feedback",
        "good",
        "great",
        "improve",
        "like",
        "love",
        "nice",
        "suggest",
    },
    "Command": {
        "analyze",
        "calculate",
        "create",
        "define",
        "describe",
        "help",
        "list",
        "open",
        "show",
        "summarize",
    },
}

FALLBACK_STOPWORDS = {
    "a",
    "an",
    "and",
    "are",
    "as",
    "at",
    "be",
    "by",
    "for",
    "from",
    "has",
    "in",
    "is",
    "it",
    "of",
    "on",
    "that",
    "the",
    "to",
    "was",
    "were",
    "will",
    "with",
    "you",
    "your",
}


def initialize_nltk_resources():
    for package in ("punkt", "punkt_tab", "stopwords", "vader_lexicon"):
        try:
            nltk.download(package, quiet=True)
        except Exception:
            continue


def load_spacy_pipeline():
    try:
        return spacy.load("en_core_web_sm"), "en_core_web_sm"
    except Exception:
        nlp = spacy.blank("en")
        nlp.add_pipe("sentencizer")
        return nlp, "spacy.blank('en') fallback"


def tokenize_with_nltk(text):
    try:
        return word_tokenize(text)
    except Exception:
        return re.findall(r"[A-Za-z0-9']+", text)


def stop_words():
    try:
        return set(stopwords.words("english"))
    except Exception:
        return FALLBACK_STOPWORDS


def analyze_sentiment(text, normalized_tokens):
    try:
        score = SentimentIntensityAnalyzer().polarity_scores(text)["compound"]
        if score >= 0.05:
            return "Positive", round(score, 3)
        if score <= -0.05:
            return "Negative", round(score, 3)
        return "Neutral", round(score, 3)
    except Exception:
        positive_count = sum(1 for token in normalized_tokens if token in POSITIVE_WORDS)
        negative_count = sum(1 for token in normalized_tokens if token in NEGATIVE_WORDS)
        score = positive_count - negative_count
        if score > 0:
            return "Positive", float(score)
        if score < 0:
            return "Negative", float(score)
        return "Neutral", 0.0


def extract_keywords(doc, normalized_tokens):
    common_words = stop_words()
    candidates = []

    for token in doc:
        text = token.text.lower()
        lemma = token.lemma_.lower() if token.lemma_ else text
        if text.isalnum() and text not in common_words and len(text) > 2:
            candidates.append(lemma)

    if not candidates:
        candidates = [
            token
            for token in normalized_tokens
            if token.isalnum() and token not in common_words and len(token) > 2
        ]

    return [keyword for keyword, _ in Counter(candidates).most_common(8)]


def extract_lemmas(doc, normalized_tokens):
    lemmas = []
    for token in doc:
        text = token.text.lower()
        if not re.search(r"[a-z0-9]", text):
            continue
        lemma = token.lemma_.lower() if token.lemma_ else text
        lemmas.append(lemma)

    return lemmas or normalized_tokens


def classify_text(normalized_tokens):
    token_set = set(normalized_tokens)
    scores = {
        label: len(token_set.intersection(keywords))
        for label, keywords in CLASSIFICATION_RULES.items()
    }
    label, score = max(scores.items(), key=lambda item: item[1])
    return label if score > 0 else "General Message"


def build_bot_response(message, analysis):
    normalized_message = message.lower()
    keywords = analysis["keywords"]

    if any(greeting in normalized_message for greeting in ("hello", "hi", "hey")):
        return "Hello! I am your PHP NLP assistant powered by NLTK and spaCy."

    if "token" in normalized_message:
        return f"Tokenization found {len(analysis['tokens'])} tokens."

    if "sentiment" in normalized_message:
        return (
            f"The message sentiment is {analysis['sentiment']} "
            f"with score {analysis['sentiment_score']}."
        )

    if "keyword" in normalized_message:
        return (
            f"The strongest keywords are: {', '.join(keywords)}."
            if keywords
            else "I did not detect strong keywords after stop-word filtering."
        )

    if analysis["sentiment"] == "Positive":
        return "That sounds positive. I extracted the important terms for review."

    if analysis["sentiment"] == "Negative":
        return "I detected a negative tone. The keywords can help identify the concern."

    if analysis["classification"] == "Inquiry":
        return "That looks like a question. I analyzed its tokens, sentiment, keywords, and category."

    return "I processed your message with NLTK tokenization, VADER sentiment, spaCy keywords, and text classification."


def analyze_text(text):
    initialize_nltk_resources()
    nlp, spacy_pipeline = load_spacy_pipeline()
    doc = nlp(text)

    tokens = tokenize_with_nltk(text)
    normalized_tokens = [token.lower() for token in tokens if re.search(r"[A-Za-z0-9]", token)]
    sentiment, sentiment_score = analyze_sentiment(text, normalized_tokens)
    keywords = extract_keywords(doc, normalized_tokens)
    lemmas = extract_lemmas(doc, normalized_tokens)
    classification = classify_text(normalized_tokens)
    entities = [
        {"text": entity.text, "label": entity.label_}
        for entity in getattr(doc, "ents", [])
    ]

    analysis = {
        "tokens": tokens,
        "normalized_tokens": normalized_tokens,
        "lemmas": lemmas,
        "sentiment": sentiment,
        "sentiment_score": sentiment_score,
        "keywords": keywords,
        "classification": classification,
        "entities": entities,
        "nlp_tools": ["NLTK", "spaCy"],
        "spacy_pipeline": spacy_pipeline,
    }
    analysis["response"] = build_bot_response(text, analysis)
    return analysis


def main():
    payload = json.load(sys.stdin)
    message = str(payload.get("message", "")).strip()
    if not message:
        raise ValueError("Message is required.")
    print(json.dumps(analyze_text(message)))


if __name__ == "__main__":
    main()
