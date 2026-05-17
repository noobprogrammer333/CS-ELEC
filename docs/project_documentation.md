# NLP-Based Intelligent Web Application Development

## Project Title

NLP Assistant: PHP Chatbot and Text Analyzer with Supabase

## Project Overview

This web application demonstrates Natural Language Processing (NLP) by
accepting human language through typed text or browser microphone input,
processing the language through a PHP backend, calling Python NLP tools, and
returning intelligent analysis and chatbot responses. Conversation history is
stored in Supabase.

## Main Objective

Build a functional web-based system that applies NLP concepts to understand,
analyze, classify, and respond to user input in natural language.

## Selected Technology Stack

- Frontend: HTML
- Supporting assets: CSS, Bootstrap, and JavaScript
- Backend: PHP
- Database: Supabase
- NLP tools: NLTK and spaCy
- Speech features: Browser Web Speech API and Speech Synthesis API

## Implemented NLP Concepts

1. Tokenization
   - NLTK tokenizes the user message and the frontend displays each token.

2. Sentiment Analysis
   - NLTK VADER classifies messages as Positive, Negative, or Neutral.
   - A word-list fallback is included for demo reliability.

3. Voice-to-Text
   - The Voice button uses the browser microphone through the Web Speech API.

4. Text-to-Speech
   - The Speak Last Response button reads the chatbot response aloud through the
     browser Speech Synthesis API.

5. Chatbot System
   - The chat interface accepts user messages, generates bot responses, and saves
     the conversation through the PHP backend.

6. Keyword Extraction
   - spaCy processes the text and helps identify important terms after stop-word
     filtering.

7. Text Classification
   - Rule-based classification labels messages as Complaint, Inquiry, Feedback,
     Command, or General Message.

8. Entity Extraction
   - spaCy entities are included when the installed spaCy model supports entity
     recognition.

## System Features

- User input through text or voice
- PHP NLP API endpoints
- Python NLP processor using NLTK and spaCy
- Chatbot response generation
- Text output and speech output
- Supabase storage for conversation history
- Responsive HTML interface
- Navigation system for Chatbot, Analyzer, History, and About sections
- Supabase SQL schema deliverable
- System flowchart and ER diagram files

## System Workflow

1. User types a message or records voice input.
2. JavaScript sends the message to a PHP endpoint using `fetch`.
3. PHP calls `nlp/nlp_processor.py`.
4. The Python processor uses NLTK and spaCy for tokenization, sentiment,
   keyword extraction, entity extraction, and classification.
5. PHP stores the conversation and NLP metadata in Supabase through the REST API.
6. The frontend displays the chat response and NLP analysis.
7. The user may click Speak Last Response to hear the output.

## Backend Routes

| Route | Method | Purpose |
| --- | --- | --- |
| `/index.php` | GET | Loads the main HTML web interface |
| `/api/chat.php` | POST | Processes chat input, saves to Supabase, returns bot response |
| `/api/analyze.php` | POST | Runs NLP analysis without saving chat history |
| `/api/history.php` | GET | Returns saved Supabase conversation records |
| `/api/history.php` | DELETE | Clears saved Supabase conversation records |

## Supabase Database Design

Table: `chats`

| Field | Type | Description |
| --- | --- | --- |
| `id` | bigint | Primary key |
| `user_message` | text | User input |
| `bot_response` | text | Generated chatbot answer |
| `tokens` | jsonb | Token list |
| `keywords` | jsonb | Keyword list |
| `sentiment` | varchar | Positive, Negative, or Neutral |
| `sentiment_score` | numeric | Numeric sentiment score |
| `classification` | varchar | Message category |
| `entities` | jsonb | spaCy entities |
| `created_at` | timestamptz | Record creation time |

## How to Run

```bash
cp .env.example .env
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
python3 -m spacy download en_core_web_sm
php -S 127.0.0.1:8000
```

Before running, create a Supabase project, run `database/nlp_assistant.sql` in
the Supabase SQL Editor, and place your Supabase URL/key in `.env`.

Open `http://127.0.0.1:8000` in a browser.

## Defense Demonstration Script

1. Open the home page and explain the selected stack: HTML, PHP, Supabase,
   NLTK, and spaCy.
2. Type: `Hello, I love this helpful NLP chatbot`.
3. Show the bot reply, tokens, sentiment, keywords, and classification.
4. Click Speak Last Response.
5. Click Voice and speak a short message if the browser supports microphone
   access.
6. Open Smart Text Analyzer and analyze another sentence.
7. Open Supabase Conversation History and show saved records.
8. Explain the SQL export and diagrams in the repository.

## Common Oral Defense Answers

### What is NLP?

Natural Language Processing is a field of AI that allows computers to process,
analyze, and respond to human language.

### How is NLP applied in this system?

The PHP backend sends user input to a Python NLP processor. NLTK performs
tokenization and sentiment analysis, while spaCy helps with keyword and entity
processing. The results guide the chatbot response.

### How does the chatbot generate responses?

The chatbot uses transparent rule-based logic. It checks the detected intent,
sentiment, keywords, and classification, then chooses an appropriate response.

### How does sentiment analysis work?

The system uses NLTK VADER when available. VADER returns a compound polarity
score. Positive scores produce Positive sentiment, negative scores produce
Negative sentiment, and near-zero scores produce Neutral sentiment.

### How can the system be improved?

Future improvements could add login, stricter Supabase row-level security, a
trained machine-learning classifier, multilingual NLP, or an LLM API for more
advanced responses.
