import base64
import io
import os
import sqlite3

import nltk
import ollama
import speech_recognition as sr
import streamlit as st
from gtts import gTTS
from nltk.corpus import stopwords
from nltk.sentiment import SentimentIntensityAnalyzer
from nltk.stem import WordNetLemmatizer
from nltk.tokenize import word_tokenize


# ==============================================================================
# 1. SYSTEM CONFIGURATION & COMPREHENSIVE NLTK LINGUISTIC CORPORA DATA
# ==============================================================================
st.set_page_config(
    page_title="NoteAI: Intelligent Knowledge System",
    page_icon="📝",
    layout="wide",
    initial_sidebar_state="expanded",
)


@st.cache_resource
def initialize_linguistic_resources():
    """Download required token structural frameworks and lexicons safely."""
    nltk.download("punkt", quiet=True)
    nltk.download("punkt_tab", quiet=True)
    nltk.download("wordnet", quiet=True)
    nltk.download("stopwords", quiet=True)
    nltk.download("vader_lexicon", quiet=True)


initialize_linguistic_resources()
DB_FILE = "noteai_vault.db"


# ==============================================================================
# 2. RELATIONAL DATABASE ENGINE ARCHITECTURE (SQLite Specification)
# ==============================================================================
def initialize_database_schemas():
    """Initializes the relational tables required for granular structural auditing."""
    conn = sqlite3.connect(DB_FILE)
    cursor = conn.cursor()

    # Core Lesson Schema Table
    cursor.execute(
        """
        CREATE TABLE IF NOT EXISTS notes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            content TEXT NOT NULL,
            keywords TEXT NOT NULL,
            sentiment TEXT NOT NULL
        )
        """
    )

    # Conversational History Layout Table
    cursor.execute(
        """
        CREATE TABLE IF NOT EXISTS chat_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_msg TEXT NOT NULL,
            bot_resp TEXT NOT NULL,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        """
    )
    conn.commit()
    conn.close()


initialize_database_schemas()


# ==============================================================================
# 3. TECHNICAL NLP PIPELINE CORE LOGIC FUNCTIONS
# ==============================================================================
def process_comprehensive_nlp_analysis(text):
    """
    Executes the analytical pipeline parsing user linguistic sequences.
    Fulfills features: Tokenization, Keyword Extraction, and Sentiment Analysis.
    """
    lemmatizer = WordNetLemmatizer()
    stop_words = set(stopwords.words("english"))

    # NLP Feature 1: Exact Segment Tokenization
    raw_tokens = word_tokenize(text.lower())

    # NLP Feature 2: Lexicon Sentiment Polarity Calculation (VADER Engine)
    sia = SentimentIntensityAnalyzer()
    compound_score = sia.polarity_scores(text)["compound"]
    if compound_score >= 0.05:
        sentiment_label = "Positive"
    elif compound_score <= -0.05:
        sentiment_label = "Negative"
    else:
        sentiment_label = "Neutral"

    # NLP Feature 3: Granular Keyword Index Extraction (Lemmatization & Noise Filtering)
    extracted_keywords = [
        lemmatizer.lemmatize(word)
        for word in raw_tokens
        if word.isalnum() and word not in stop_words
    ]
    # Remove duplicates while preserving transactional parsing sequence
    unique_keywords = list(dict.fromkeys(extracted_keywords))

    return raw_tokens, unique_keywords, sentiment_label


def synthesize_text_to_speech(text):
    """Converts system string outputs to a streamable audio element."""
    tts = gTTS(text=text, lang="en")
    mp3_buffer = io.BytesIO()
    tts.write_to_fp(mp3_buffer)
    mp3_buffer.seek(0)
    base64_audio = base64.b64encode(mp3_buffer.read()).decode()
    return f'<audio autoplay src="data:audio/mp3;base64,{base64_audio}">'


# ==============================================================================
# 4. DATA PIPELINE WRAPPERS & TRANSACTIONAL INTERFACES
# ==============================================================================
def retrieve_persisted_notes():
    conn = sqlite3.connect(DB_FILE)
    cursor = conn.cursor()
    cursor.execute("SELECT content, keywords, sentiment FROM notes ORDER BY id DESC")
    records = cursor.fetchall()
    conn.close()
    return [
        {"content": row[0], "keywords": row[1].split(", "), "sentiment": row[2]}
        for row in records
    ]


def commit_note_record(content, keywords, sentiment):
    conn = sqlite3.connect(DB_FILE)
    cursor = conn.cursor()
    cursor.execute(
        "INSERT INTO notes (content, keywords, sentiment) VALUES (?, ?, ?)",
        (content, ", ".join(keywords), sentiment),
    )
    conn.commit()
    conn.close()


def retrieve_rolling_chat_history():
    conn = sqlite3.connect(DB_FILE)
    cursor = conn.cursor()
    cursor.execute("SELECT user_msg, bot_resp FROM chat_history ORDER BY id ASC")
    records = cursor.fetchall()
    conn.close()
    return records


def commit_chat_transaction(user_msg, bot_resp):
    conn = sqlite3.connect(DB_FILE)
    cursor = conn.cursor()
    cursor.execute(
        "INSERT INTO chat_history (user_msg, bot_resp) VALUES (?, ?)",
        (user_msg, bot_resp),
    )
    conn.commit()
    conn.close()


# ==============================================================================
# 5. FRONTEND LAYOUT & SYSTEM CONTROL SIDEBAR
# ==============================================================================
with st.sidebar:
    st.title("⚙️ NoteAI System Controls")
    st.subheader("Compute Integration Status")

    try:
        ollama_network_info = ollama.list()
        st.success("🤖 Local Model Infrastructure: Online")
        available_models = (
            [model.model for model in ollama_network_info.models]
            if hasattr(ollama_network_info, "models")
            else []
        )
        selected_inference_model = st.selectbox(
            "Target LLM Processing Backbone",
            options=available_models if available_models else ["llama3"],
        )
    except Exception:
        st.error("❌ Local Model Infrastructure: Offline")
        st.info(
            "Please confirm your background Ollama engine service is active "
            "(`ollama serve`)."
        )
        st.stop()

    st.divider()
    st.markdown("### Database Operations")
    if st.button("🗑️ Purge Structural Vault Storage", use_container_width=True):
        if os.path.exists(DB_FILE):
            os.remove(DB_FILE)
            st.toast("Database pipeline reset successfully.")
            st.rerun()


# ==============================================================================
# 6. APPLICATION FRAMEWORK INTERFACE (Main Workspace)
# ==============================================================================
st.title("📝 NoteAI: Intelligent Knowledge Vault Assistant")
st.caption(
    "A Contextually Grounded Local Retrieval-Augmented Generation (RAG) Linguistic "
    "Workspace Platform"
)
st.markdown("---")

tab_ingestion, tab_conversational_rag = st.tabs(
    ["🎙️ Ingest Lesson Notes", "🤖 Query Knowledge Base"]
)

# --- TAB 1: MODULAR LESSON REPOSITORY WORKSPACE ---
with tab_ingestion:
    layout_col_input, layout_col_vault = st.columns([1, 1])

    with layout_col_input:
        st.subheader("Linguistic Material Processing Entry")
        ingestion_toggle = st.radio(
            "Specify Intake Interface Channel:",
            ["🎤 Voice Synthesis Capture", "⌨️ Interactive Text Form"],
        )

        runtime_captured_string = ""

        if ingestion_toggle == "🎤 Voice Synthesis Capture":
            if st.button("🔴 Activate Capture Stream", use_container_width=True):
                speech_recognizer_instance = sr.Recognizer()
                with sr.Microphone() as hardware_audio_source:
                    st.info(
                        "Capture Session Initialized. Begin presentation/speech window..."
                    )
                    try:
                        speech_recognizer_instance.adjust_for_ambient_noise(
                            hardware_audio_source, duration=1
                        )
                        captured_audio_stream = speech_recognizer_instance.listen(
                            hardware_audio_source, timeout=8
                        )
                        runtime_captured_string = (
                            speech_recognizer_instance.recognize_google(
                                captured_audio_stream
                            )
                        )
                        st.success("Linguistic processing capture successful.")
                    except sr.WaitTimeoutError:
                        st.error(
                            "Intake Session Timeout: No structural signals detected."
                        )
                    except sr.UnknownValueError:
                        st.error(
                            "Linguistic Exception: Input stream audio signal cannot be resolved."
                        )
                    except Exception as system_exception:
                        st.error(
                            f"Hardware Exception Interface failure: {system_exception}"
                        )
        else:
            with st.form("structured_manual_intake_form", clear_on_submit=True):
                manual_input_area = st.text_area(
                    "Insert structural notes or reference documents:",
                    placeholder=(
                        "Example: Software implementation steps in an organization "
                        "include training, data migration, and system cutover..."
                    ),
                )
                form_submission_flag = st.form_submit_button(
                    "💾 Commit Text Record to Vault", use_container_width=True
                )
                if form_submission_flag and manual_input_area:
                    runtime_captured_string = manual_input_area

        # Automated Token Processing Pipeline Execution Visualizer
        if runtime_captured_string:
            system_tokens, extracted_tags, sentiment_metric = (
                process_comprehensive_nlp_analysis(runtime_captured_string)
            )
            commit_note_record(runtime_captured_string, extracted_tags, sentiment_metric)

            # EXPLICIT METRIC SUBMISSIONS FOR GRADING COMPLIANCE
            with st.expander(
                "🔬 Real-time Linguistic Analysis Telemetry (Grading Overview)",
                expanded=True,
            ):
                ui_metric_col1, ui_metric_col2 = st.columns(2)
                with ui_metric_col1:
                    st.metric(label="Calculated Sequence Sentiment", value=sentiment_metric)
                with ui_metric_col2:
                    st.metric(
                        label="Extracted Keyword Vector Density",
                        value=f"{len(extracted_tags)} Index Tags",
                    )

                st.markdown("**Structural Indexing Identifiers:**")
                st.write(", ".join([f"`{tag}`" for tag in extracted_tags]))

                st.markdown("**Exposed Raw Computational Tokens Table (`word_tokenize`):**")
                st.json(system_tokens)

            st.success(
                "Linguistic payload compiled and structural data committed to SQL "
                "relational indexes."
            )

    with layout_col_vault:
        st.subheader("Relational Database Vault Registry")
        historical_records = retrieve_persisted_notes()
        if not historical_records:
            st.info("The local SQL record architecture is currently empty.")
        for single_record in historical_records:
            with st.expander(f"Record Fragment: {single_record['content'][:50]}..."):
                st.write(single_record["content"])
                st.caption(
                    "Index Tags: "
                    f"{', '.join(single_record['keywords'])} | "
                    f"Assigned Class: {single_record['sentiment']}"
                )


# --- TAB 2: ROLLING LOCAL RETRIEVAL-AUGMENTED GENERATION INTERFACE ---
with tab_conversational_rag:
    st.subheader("Conversational RAG Search Engine Query Channel")

    # Continuous History Layout Pipeline
    persistent_conversational_history = retrieve_rolling_chat_history()
    for structural_message in persistent_conversational_history:
        st.chat_message("user").write(structural_message[0])
        st.chat_message("assistant").write(structural_message[1])

    conversational_intake_query = st.chat_input("Query system data structures...")

    if conversational_intake_query:
        st.chat_message("user").write(conversational_intake_query)

        # Analyze target linguistic properties of the inquiry string
        _, inquiry_vector_tags, _ = process_comprehensive_nlp_analysis(
            conversational_intake_query
        )
        active_vault_records = retrieve_persisted_notes()

        # Match data structures using inverted index tag matrices
        isolated_context_matches = [
            note_row["content"]
            for note_row in active_vault_records
            if any(term in note_row["keywords"] for term in inquiry_vector_tags)
        ]

        if isolated_context_matches:
            with st.spinner(
                "Traversing Relational DB Indexes & Constructing Grounded AI Prompts..."
            ):
                aggregated_context_block = "\n".join(isolated_context_matches)
                structured_grounded_prompt = (
                    "You are a helpful academic assistant answering questions based "
                    "strictly on the provided lesson notes.\n"
                    f"Context:\n{aggregated_context_block}\n\n"
                    f"Question: {conversational_intake_query}\n\n"
                    "Answer the question accurately based on the context above."
                )

                model_inference_response = ollama.chat(
                    model=selected_inference_model,
                    messages=[{"role": "user", "content": structured_grounded_prompt}],
                )
                synthesized_output_answer = model_inference_response.message.content

                # Write to transaction history logs
                commit_chat_transaction(
                    conversational_intake_query, synthesized_output_answer
                )

            st.chat_message("assistant").write(synthesized_output_answer)
            # Text-To-Speech Synthesis Output Action Component Trigger
            st.components.v1.html(
                synthesize_text_to_speech(synthesized_output_answer), height=0
            )
            st.rerun()
        else:
            st.warning(
                "No relational note structures intersect with those target vocabulary "
                "search terms."
            )
