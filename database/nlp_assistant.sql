CREATE DATABASE IF NOT EXISTS nlp_assistant_db;
USE nlp_assistant_db;

DROP TABLE IF EXISTS chats;

CREATE TABLE chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_message TEXT NOT NULL,
    bot_response TEXT NOT NULL,
    tokens TEXT NOT NULL,
    keywords TEXT NOT NULL,
    sentiment VARCHAR(20) NOT NULL,
    sentiment_score DECIMAL(6, 3) NOT NULL,
    classification VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO chats (
    user_message,
    bot_response,
    tokens,
    keywords,
    sentiment,
    sentiment_score,
    classification
) VALUES (
    'I love this NLP chatbot because it is helpful',
    'That sounds positive. I also extracted the key ideas so you can review them below.',
    'I, love, this, NLP, chatbot, because, it, is, helpful',
    'love, nlp, chatbot, helpful',
    'Positive',
    0.850,
    'Feedback'
);
