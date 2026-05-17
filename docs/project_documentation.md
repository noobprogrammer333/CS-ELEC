# NLP-Based Intelligent Web Application Development

## Project Title

NoteAI Lessons: Speech-to-Text Notes and AI Lesson Assistant

## Project Overview

This web application demonstrates Natural Language Processing (NLP) through a
phone-style notes workflow. Students can type or dictate lesson notes, save
those notes to Supabase, and see NLP analysis for each note. A chatbot-style AI
assistant then scans saved notes and returns the lesson content that matches a
student's question.

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
   - NLTK tokenizes each saved note and the frontend displays each token.

2. Lemmatization
   - spaCy normalizes note words to lemma/base forms for better note search.

3. Sentiment Analysis
   - NLTK VADER classifies messages as Positive, Negative, or Neutral.
   - A word-list fallback is included for demo reliability.

4. Voice-to-Text
   - The Dictate Note button uses the browser microphone and writes speech into
     the note editor.

5. Text-to-Speech
   - The Speak Answer button reads the assistant response aloud through the
     browser Speech Synthesis API.

6. Notes-Aware Chatbot System
   - The assistant accepts direct lesson questions, scans saved notes, returns
     matching notes, and saves the Q&A through the PHP backend.

7. Keyword Extraction
   - spaCy processes the text and helps identify important terms after stop-word
     filtering.

8. Text Classification
   - Rule-based classification labels messages as Complaint, Inquiry, Feedback,
     Command, or General Message.

9. Entity Extraction
   - spaCy entities are included when the installed spaCy model supports entity
     recognition.

## System Features

- User input through text or voice
- PHP NLP API endpoints
- Python NLP processor using NLTK and spaCy
- Phone-style note creation
- Speech-to-text note writing
- NLP metadata for saved notes
- Notes-aware chatbot response generation
- Text output and speech output
- Supabase storage for notes and assistant Q&A history
- Responsive HTML interface
- Navigation system for Notes, AI Assistant, Q&A History, and About sections
- Supabase SQL schema deliverable
- System flowchart and ER diagram files

## System Workflow

1. User types a lesson note or records voice input into the note editor.
2. JavaScript sends the note to `api/notes.php`.
3. PHP calls `nlp/nlp_processor.py`.
4. The Python processor uses NLTK and spaCy for tokenization, lemmatization,
   sentiment, keyword extraction, entity extraction, and classification.
5. PHP stores the note and NLP metadata in Supabase.
6. User asks the AI assistant a lesson question.
7. `api/chat.php` analyzes the question and scans saved notes by keywords,
   lemmas, and content matches.
8. The assistant returns matching lesson notes and the answer can be spoken
   aloud.

## Backend Routes

| Route | Method | Purpose |
| --- | --- | --- |
| `/index.php` | GET | Loads the main HTML web interface |
| `/api/notes.php` | GET | Lists saved notes from Supabase |
| `/api/notes.php` | POST | Saves a note with NLP metadata |
| `/api/notes.php` | DELETE | Clears saved notes |
| `/api/chat.php` | POST | Scans saved notes and returns an assistant answer |
| `/api/analyze.php` | POST | Runs NLP analysis without saving chat history |
| `/api/history.php` | GET | Returns saved assistant Q&A records |
| `/api/history.php` | DELETE | Clears assistant Q&A records |

## Supabase Database Design

Table: `notes`

| Field | Type | Description |
| --- | --- | --- |
| `id` | bigint | Primary key |
| `title` | text | Note title |
| `content` | text | Full lesson note |
| `tokens` | jsonb | Token list |
| `lemmas` | jsonb | Lemmatized terms |
| `keywords` | jsonb | Keyword list |
| `sentiment` | varchar | Positive, Negative, or Neutral |
| `sentiment_score` | numeric | Numeric sentiment score |
| `classification` | varchar | Message category |
| `entities` | jsonb | spaCy entities |
| `created_at` | timestamptz | Record creation time |
| `updated_at` | timestamptz | Last update time |

Table: `assistant_chats`

| Field | Type | Description |
| --- | --- | --- |
| `id` | bigint | Primary key |
| `question` | text | Student question |
| `answer` | text | Assistant response from matching notes |
| `query_tokens` | jsonb | Tokenized question |
| `query_keywords` | jsonb | Question keywords |
| `matched_note_ids` | jsonb | Notes used as sources |
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
2. Create a note titled `Software Implementation Lesson`.
3. Use Dictate Note or type: `Software implementation includes training, data
   migration, testing, and cutover`.
4. Save the note and show tokens, lemmas, sentiment, keywords, and
   classification.
5. Ask: `What are the steps in software implementation?`.
6. Show that the assistant scans notes and returns the matching lesson.
7. Click Speak Answer.
8. Open Assistant Q&A History and show saved records.
9. Explain the SQL export and diagrams in the repository.

## Common Oral Defense Answers

### What is NLP?

Natural Language Processing is a field of AI that allows computers to process,
analyze, and respond to human language.

### How is NLP applied in this system?

The PHP backend sends each note and question to a Python NLP processor. NLTK
performs tokenization and sentiment analysis, while spaCy helps with
lemmatization, keyword extraction, and entity processing. The assistant compares
question keywords with saved note keywords and lemmas to find relevant lessons.

### How does the chatbot generate responses?

The assistant analyzes the question, scans saved notes in Supabase, ranks notes
by keyword, lemma, and content matches, then returns the best matching lesson
snippets as its answer.

### How does sentiment analysis work?

The system uses NLTK VADER when available. VADER returns a compound polarity
score. Positive scores produce Positive sentiment, negative scores produce
Negative sentiment, and near-zero scores produce Neutral sentiment.

### How can the system be improved?

Future improvements could add login, stricter Supabase row-level security, a
trained machine-learning classifier, multilingual NLP, or an LLM API for more
advanced responses.
