import re
from collections import Counter

try:
    import nltk
    from nltk.corpus import stopwords
    from nltk.sentiment import SentimentIntensityAnalyzer
    from nltk.stem import WordNetLemmatizer
    from nltk.tokenize import word_tokenize
except ImportError:  # Fallbacks keep the demo usable if NLTK is not installed yet.
    nltk = None
    stopwords = None
    SentimentIntensityAnalyzer = None
    WordNetLemmatizer = None
    word_tokenize = None


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
    "he",
    "in",
    "is",
    "it",
    "its",
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


def initialize_nltk_resources():
    """Download optional NLTK resources used for stronger NLP analysis."""
    if nltk is None:
        return

    for package in ("punkt", "punkt_tab", "wordnet", "stopwords", "vader_lexicon"):
        try:
            nltk.download(package, quiet=True)
        except Exception:
            # Browser demo still works through rule-based fallbacks.
            continue


def tokenize_text(text):
    """Break user input into tokens, with a regex fallback for reliability."""
    cleaned_text = text.strip()
    if not cleaned_text:
        return []

    if word_tokenize is not None:
        try:
            return word_tokenize(cleaned_text)
        except Exception:
            pass

    return re.findall(r"[A-Za-z0-9']+", cleaned_text)


def normalize_tokens(tokens):
    return [token.lower() for token in tokens if re.search(r"[A-Za-z0-9]", token)]


def get_stopwords():
    if stopwords is not None:
        try:
            return set(stopwords.words("english"))
        except Exception:
            pass
    return FALLBACK_STOPWORDS


def lemmatize_token(token):
    if WordNetLemmatizer is not None:
        try:
            return WordNetLemmatizer().lemmatize(token)
        except Exception:
            pass
    return token


def analyze_sentiment(text, normalized_tokens):
    """Classify sentiment as Positive, Negative, or Neutral."""
    if SentimentIntensityAnalyzer is not None:
        try:
            score = SentimentIntensityAnalyzer().polarity_scores(text)["compound"]
            if score >= 0.05:
                return "Positive", score
            if score <= -0.05:
                return "Negative", score
            return "Neutral", score
        except Exception:
            pass

    positive_count = sum(1 for token in normalized_tokens if token in POSITIVE_WORDS)
    negative_count = sum(1 for token in normalized_tokens if token in NEGATIVE_WORDS)
    score = positive_count - negative_count
    if score > 0:
        return "Positive", float(score)
    if score < 0:
        return "Negative", float(score)
    return "Neutral", 0.0


def extract_keywords(normalized_tokens):
    stop_words = get_stopwords()
    candidates = [
        lemmatize_token(token)
        for token in normalized_tokens
        if token.isalnum() and token not in stop_words and len(token) > 2
    ]
    ranked_keywords = Counter(candidates).most_common()
    return [keyword for keyword, _ in ranked_keywords[:8]]


def classify_text(normalized_tokens):
    token_set = set(normalized_tokens)
    scores = {
        label: len(token_set.intersection(keywords))
        for label, keywords in CLASSIFICATION_RULES.items()
    }
    label, score = max(scores.items(), key=lambda item: item[1])
    return label if score > 0 else "General Message"


def build_bot_response(message, analysis):
    """Generate a transparent rule-based chatbot response from NLP output."""
    normalized_message = message.lower()
    keywords = analysis["keywords"]
    sentiment = analysis["sentiment"]
    category = analysis["classification"]

    if any(greeting in normalized_message for greeting in ("hello", "hi", "hey")):
        return "Hello! I am your NLP assistant. Send text or use the microphone and I will analyze it."

    if "token" in normalized_message:
        return f"Tokenization found {len(analysis['tokens'])} tokens: {', '.join(analysis['tokens'])}."

    if "sentiment" in normalized_message:
        return f"The message sentiment is {sentiment} with a score of {analysis['sentiment_score']}."

    if "keyword" in normalized_message:
        if keywords:
            return f"The strongest keywords are: {', '.join(keywords)}."
        return "I did not find strong keywords after removing common stop words."

    if "class" in normalized_message or "category" in normalized_message:
        return f"I classified this input as: {category}."

    if sentiment == "Positive":
        return "That sounds positive. I also extracted the key ideas so you can review them below."

    if sentiment == "Negative":
        return "I detected a negative tone. I can help identify the main issue from the keywords below."

    if category == "Inquiry":
        return "That looks like a question. I analyzed its tokens, keywords, sentiment, and category for you."

    if category == "Command":
        return "Command received. I processed the text and displayed the NLP results in the analysis panel."

    return "I processed your message using tokenization, sentiment analysis, keyword extraction, and classification."


def analyze_text(text):
    tokens = tokenize_text(text)
    normalized_tokens = normalize_tokens(tokens)
    sentiment, sentiment_score = analyze_sentiment(text, normalized_tokens)
    keywords = extract_keywords(normalized_tokens)
    classification = classify_text(normalized_tokens)

    analysis = {
        "tokens": tokens,
        "normalized_tokens": normalized_tokens,
        "sentiment": sentiment,
        "sentiment_score": round(sentiment_score, 3),
        "keywords": keywords,
        "classification": classification,
    }
    analysis["response"] = build_bot_response(text, analysis)
    return analysis
