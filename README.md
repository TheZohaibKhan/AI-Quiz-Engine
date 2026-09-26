# AI Quiz Engine

An AI-powered quiz and examination platform built with **Laravel 12** and **Livewire 4**. The platform provides separate workflows for teachers and students, with AI-assisted question generation, answer evaluation, contextual hints, similarity search, usage tracking, and examination management.

## 🚀 Overview

AI Quiz Engine is a full-stack web application designed to simplify the creation, management, and evaluation of online examinations.

Teachers can create examinations, manage question banks, generate questions using AI, monitor AI usage, and review student results.

Students can take examinations, submit answers, view results, review attempts, and participate in exam leaderboards.

The application combines Laravel's backend capabilities with Livewire's reactive interface and PostgreSQL/pgvector for AI-powered similarity search.

---

## 🌐 Live Demo

**Live Application:**

https://ai-quiz-engine-b049.onrender.com

**GitHub Repository:**

https://github.com/TheZohaibKhan/AI-Quiz-Engine

---

## ✨ Features

### 👨‍🏫 Teacher Features

- Create and manage examinations
- Edit existing examinations
- Add and manage exam questions
- Maintain a question bank
- Generate questions using AI
- Review examination results
- Monitor AI usage
- Manage AI-assisted examination workflows
- Import questions using CSV
- Export examination/result information as PDF

### 👨‍🎓 Student Features

- Student dashboard
- View available examinations
- Take online examinations
- Submit examination attempts
- View examination results
- View previous attempts
- View exam leaderboards
- Receive AI-powered assistance where enabled

---

## 🤖 AI Capabilities

The platform integrates AI into multiple parts of the examination workflow.

### AI Question Generation

Teachers can use AI to generate examination questions based on the configured examination content.

### AI Answer Grading

The platform supports AI-assisted evaluation of short-answer responses, including partial-credit grading.

### Socratic Hints

Students can receive AI-generated hints designed to guide their reasoning rather than directly providing the answer.

### Similarity Search

The application uses embeddings and vector similarity search to retrieve semantically related questions and information.

### Multiple AI Providers

The application is designed to work with multiple AI providers, including:

- Anthropic
- Google Gemini
- OpenAI
- Ollama

AI provider configuration is handled through environment variables and application configuration.

---

## 🧠 AI Usage & Cost Controls

The application includes AI usage tracking and rate limiting.

Features include:

- AI request tracking
- AI usage logging
- Per-minute AI rate limiting
- Daily AI budget configuration
- Embedding usage tracking
- AI provider/model configuration

This helps control AI usage and reduce unexpected API costs.

Example configuration:

```env
AI_RATE_LIMIT_PER_MINUTE=30
AI_DAILY_BUDGET=5.00