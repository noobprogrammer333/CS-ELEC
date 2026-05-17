# NLP-Based Intelligent Web Application Development

## Project Title

NLP Assistant: Intelligent Chatbot and Text Analyzer

## Project Overview

This web application demonstrates Natural Language Processing (NLP) by
accepting human language through typed text or browser microphone input,
processing the language on a Flask backend, and returning intelligent analysis
and chatbot responses. The system is designed for a live classroom defense and
includes visible proof of each NLP step.

## Main Objective

Build a functional web-based system that applies NLP concepts to understand,
analyze, classify, and respond to user input in natural language.

## Technology Stack

- Frontend: HTML, CSS, Bootstrap, JavaScript
- Backend: Python Flask
- NLP: NLTK with rule-based fallbacks
- Database: SQLite runtime database plus SQL export in `database/nlp_assistant.sql`
- Speech Features: Browser Web Speech API and Speech Synthesis API

## Implemented NLP Concepts

1. Tokenization
   - The system breaks the user message into tokens and displays each token as a
     visible chip in the NLP output panel.

2. Sentiment Analysis
   - The backend evaluates whether the message is Positive, Negative, or Neutral.
   - NLTK VADER is used when available; a positive/negative word-list fallback is
     included so the demo remains functional.

3. Voice-to-Text
   - The Voice button uses the browser microphone through the Web Speech API.
   - The recognized speech is converted into text and placed in the chat input.

4. Text-to-Speech
   - The Speak Last Response button reads the chatbot response aloud through the
     browser Speech Synthesis API.

5. Chatbot System
   - The chat interface accepts user messages, generates bot responses, and stores
     each conversation in the database.

6. Keyword Extraction
   - The NLP module removes stop words, lemmatizes terms when possible, ranks
     important words, and displays the top keywords.

7. Text Classification
   - The system classifies messages into Complaint, Inquiry, Feedback, Command,
     or General Message using transparent keyword rules.

## System Features

- User input through text or voice
- NLP processing endpoint
- Chatbot response generation
- Text output and speech output
- Database storage for conversation history
- Responsive user interface
- Navigation system for Chatbot, Analyzer, History, and About sections
- SQL export for database deliverable
- System flowchart and ER diagram files

## System Workflow

1. User types a message or records voice input.
2. JavaScript sends the message to the Flask backend using `fetch`.
3. Flask calls the NLP engine.
4. The NLP engine performs tokenization, sentiment analysis, keyword extraction,
   and classification.
5. The chatbot generates a response from the NLP result.
6. The database module saves the user message, bot response, and NLP metadata.
7. The frontend displays the chat response and NLP analysis.
8. The user may click Speak Last Response to hear the output.

## Backend Routes

| Route | Method | Purpose |
| --- | --- | --- |
| `/` | GET | Loads the main web interface |
| `/api/chat` | POST | Processes chat input, saves history, returns bot response |
| `/api/analyze` | POST | Runs NLP analysis without saving chat history |
| `/api/history` | GET | Returns saved conversation records |
| `/api/history` | DELETE | Clears saved conversation records |

## Database Design

Table: `chats`

| Field | Type | Description |
| --- | --- | --- |
| `id` | Integer | Primary key |
| `user_message` | Text | User input |
| `bot_response` | Text | Generated chatbot answer |
| `tokens` | Text | Comma-separated token list |
| `keywords` | Text | Comma-separated keyword list |
| `sentiment` | Varchar | Positive, Negative, or Neutral |
| `sentiment_score` | Real/Decimal | Numeric sentiment score |
| `classification` | Varchar | Message category |
| `created_at` | Timestamp | Record creation time |

## How to Run

```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
python3 app.py
```

Open `http://127.0.0.1:5000` in a browser.

## Defense Demonstration Script

1. Open the home page and explain the navigation.
2. Type: `Hello, I love this helpful NLP chatbot`.
3. Show the bot reply, tokens, sentiment, keywords, and classification.
4. Click Speak Last Response.
5. Click Voice and speak a short message if the browser supports microphone
   access.
6. Open Smart Text Analyzer and analyze another sentence.
7. Open Database Conversation History and show saved chat records.
8. Explain the SQL export and diagrams in the repository.

## Common Oral Defense Answers

### What is NLP?

Natural Language Processing is a field of AI that allows computers to process,
analyze, and respond to human language.

### How is NLP applied in this system?

The system tokenizes user input, detects sentiment, extracts keywords,
classifies the message, and uses those NLP results to generate a chatbot
response.

### How does the chatbot generate responses?

The chatbot uses transparent rule-based logic. It checks the detected intent,
sentiment, keywords, and classification, then chooses an appropriate response.

### How does sentiment analysis work?

The system uses NLTK VADER when available. VADER returns a compound polarity
score. Positive scores produce Positive sentiment, negative scores produce
Negative sentiment, and near-zero scores produce Neutral sentiment. A word-list
fallback is included for reliability.

### How can the system be improved?

Future improvements could add user login, MySQL runtime configuration, a trained
machine-learning classifier, larger intent rules, multilingual NLP, or an LLM API
for more advanced responses.
