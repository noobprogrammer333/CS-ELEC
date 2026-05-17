# CS-ELEC NoteAI Lessons

NoteAI Lessons is a PHP-based web application for the project requirement
"NLP-Based Intelligent Web Application Development." The core idea is a
phone-style notes app: students can type or dictate lesson notes, the system
analyzes those notes with NLP, and an AI assistant scans the saved notes to
answer lesson questions.

## Implemented Requirements

- Selected frontend: HTML
- Supporting frontend assets: CSS, Bootstrap, and JavaScript for styling and
  browser interactivity
- Backend: PHP
- Database: Supabase with SQL setup script in `database/nlp_assistant.sql`
- Authentication: Supabase Auth login/register pages with PHP sessions
- NLP tools: NLTK and spaCy through `nlp/nlp_processor.py`
- NLP features:
  - Tokenization
  - Lemmatization
  - Sentiment analysis
  - Voice-to-text
  - Text-to-speech
  - Notes-aware chatbot assistant
  - Keyword extraction
  - Text classification
- Functional navigation: Notes, AI Assistant, Q&A History, About
- Required documentation: project documentation, flowchart, and ER diagram

## Getting Started

1. Clone the repository.
2. Create a Supabase project and run `database/nlp_assistant.sql` in the
   Supabase SQL Editor.
3. Copy the environment example and fill in your Supabase credentials:

   ```bash
   cp .env.example .env
   ```

4. Create and activate a Python virtual environment for the NLP processor:

   ```bash
   python3 -m venv .venv
   source .venv/bin/activate
   ```

5. Install NLTK and spaCy:

   ```bash
   pip install -r requirements.txt
   ```

6. Optional but recommended: install the spaCy English model. The app still
   works with a blank spaCy fallback if this model is unavailable.

   ```bash
   python3 -m spacy download en_core_web_sm
   ```

7. Run the PHP web application:

   ```bash
   php -S 127.0.0.1:8000
   ```

8. Open the app in a browser and register/log in:

   ```text
   http://127.0.0.1:8000/register.php
   http://127.0.0.1:8000/login.php
   ```

## Project Structure

```text
index.php                      HTML frontend served by PHP
login.php                      Supabase Auth login page
register.php                   Supabase Auth registration page
logout.php                     Session logout handler
api/notes.php                  PHP notes save/list/clear endpoint
api/chat.php                   PHP assistant endpoint that scans saved notes
api/analyze.php                PHP standalone NLP analysis endpoint
api/history.php                PHP assistant Q&A history endpoint
php/config.php                 Environment and JSON helpers
php/SupabaseClient.php         Supabase REST client
php/NlpProcessor.php           PHP bridge to Python NLP processor
nlp/nlp_processor.py           NLTK + spaCy NLP processor
requirements.txt               Python NLP dependencies
static/css/style.css           Custom responsive styling
static/js/script.js            AJAX, voice input, speech output, UI rendering
database/nlp_assistant.sql     Supabase SQL setup script
docs/project_documentation.md  Project documentation content
docs/project_documentation.pdf PDF documentation deliverable
docs/system_flowchart.mmd      Mermaid system flowchart
docs/er_diagram.mmd            Mermaid ER diagram
```

Supabase credentials are read from `.env`, which is ignored by Git.

## Demo Checklist

1. Register or log in.
2. Create a lesson note by typing text.
3. Use Dictate Note to convert speech into note text.
4. Save the note and show tokenization, lemmas, sentiment, keywords, and
   classification.
5. Ask the AI Assistant about the lesson.
6. Show that the assistant scans and cites matched notes.
7. Use Speak Answer for text-to-speech output.
8. Open the Q&A History section to show Supabase records.

## Validation

```bash
php -l index.php
php -l login.php
php -l register.php
php -l logout.php
php -l api/chat.php
php -l api/analyze.php
php -l api/history.php
php -l api/notes.php
python3 -m py_compile nlp/nlp_processor.py tests/test_nlp_processor.py
python3 -m unittest tests/test_nlp_processor.py
```
