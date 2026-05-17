import os
import sqlite3
from contextlib import closing


DB_FILE = os.getenv("NLP_ASSISTANT_DB", "nlp_assistant.db")


def get_connection():
    conn = sqlite3.connect(DB_FILE)
    conn.row_factory = sqlite3.Row
    return conn


def initialize_database():
    with closing(get_connection()) as conn:
        conn.execute(
            """
            CREATE TABLE IF NOT EXISTS chats (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_message TEXT NOT NULL,
                bot_response TEXT NOT NULL,
                tokens TEXT NOT NULL,
                keywords TEXT NOT NULL,
                sentiment VARCHAR(20) NOT NULL,
                sentiment_score REAL NOT NULL,
                classification VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """
        )
        conn.commit()


def save_chat(user_message, analysis):
    with closing(get_connection()) as conn:
        cursor = conn.execute(
            """
            INSERT INTO chats (
                user_message,
                bot_response,
                tokens,
                keywords,
                sentiment,
                sentiment_score,
                classification
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
            """,
            (
                user_message,
                analysis["response"],
                ", ".join(analysis["tokens"]),
                ", ".join(analysis["keywords"]),
                analysis["sentiment"],
                analysis["sentiment_score"],
                analysis["classification"],
            ),
        )
        conn.commit()
        return cursor.lastrowid


def fetch_chat_history(limit=50):
    with closing(get_connection()) as conn:
        rows = conn.execute(
            """
            SELECT
                id,
                user_message,
                bot_response,
                tokens,
                keywords,
                sentiment,
                sentiment_score,
                classification,
                created_at
            FROM chats
            ORDER BY id DESC
            LIMIT ?
            """,
            (limit,),
        ).fetchall()

    return [dict(row) for row in reversed(rows)]


def clear_chat_history():
    with closing(get_connection()) as conn:
        conn.execute("DELETE FROM chats")
        conn.commit()
