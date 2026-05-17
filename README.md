# CS-ELEC NLP Assistant

NLP Assistant is a PHP-based web application for the project requirement
"NLP-Based Intelligent Web Application Development." It demonstrates natural
language processing through a chatbot, smart text analyzer, voice input,
speech output, and Supabase-backed conversation history.

## Implemented Requirements

- Selected frontend: HTML
- Supporting frontend assets: CSS, Bootstrap, and JavaScript for styling and
  browser interactivity
- Backend: PHP
- Database: Supabase with SQL setup script in `database/nlp_assistant.sql`
- NLP tools: NLTK and spaCy through `nlp/nlp_processor.py`
- NLP features:
  - Tokenization
  - Sentiment analysis
  - Voice-to-text
  - Text-to-speech
  - Chatbot system
  - Keyword extraction
  - Text classification
- Functional navigation: Chatbot, Text Analyzer, History, About
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

8. Open the app in a browser:

   ```text
   http://127.0.0.1:8000
   ```

## Project Structure

```text
index.php                      HTML frontend served by PHP
api/chat.php                   PHP chatbot endpoint
api/analyze.php                PHP standalone NLP analysis endpoint
api/history.php                PHP Supabase history endpoint
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

1. Send a chat message and show the bot response.
2. Display tokenized input, sentiment, keywords, and classification.
3. Use the Voice button to convert speech into text.
4. Use Speak Last Response for text-to-speech output.
5. Open the History section to show Supabase records.
6. Open the Smart Text Analyzer for standalone NLP analysis.

## Validation

```bash
php -l index.php
php -l api/chat.php
php -l api/analyze.php
php -l api/history.php
python3 -m py_compile nlp/nlp_processor.py tests/test_nlp_processor.py
python3 -m unittest tests/test_nlp_processor.py
```
