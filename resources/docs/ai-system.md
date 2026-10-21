# RJ NEXUS - AI System

> Complete AI architecture. AI is the core of RJ NEXUS, not an add-on.

## 1. AI Philosophy

AI is not an add-on in RJ NEXUS. It is the absolute core of the platform, and every module is AI-aware.

## 2. AI Center (Admin Dashboard)

Dedicated 5-module AI management hub, backed by `AiCenterController`.

| Module | Route | Purpose |
|---|---|---|
| Overview | `admin.ai.index` | AI health, usage stats, quick actions |
| Models | `admin.ai.models` | Available models (OpenRouter), selection |
| Prompts | `admin.ai.prompts` | Prompt library (`rj_ai_prompts`) |
| Usage | `admin.ai.usage` | Token usage, costs (`rj_ai_logs`) |
| Tools | `admin.ai.tools` | AI tool runner (SEO, translate, generate, review) |

## 3. AI Providers

### 3.1 Primary: OpenRouter

- Service: `OpenRouterService`
- Models: configurable via AI Center, Models
- Config: API key in Settings, AI

### 3.2 Fallback: FreeAiService

- Service: `FreeAiService`
- Provider: Pollinations.ai (keyless)
- Used when the OpenRouter key is missing or quota is exceeded
- Powers the Dashboard chat

### 3.3 Dashboard Chat

- Full-screen AI chat on the dashboard
- RAG-backed (see section 5)
- Suggestion cards
- Gemini-style animations
- Backed by `DashboardController@askAi` and `FreeAiService`

## 4. AI Services

| Service | Purpose |
|---|---|
| `OpenRouterService` | Primary API client |
| `FreeAiService` | Fallback keyless client |
| `AiBuilderService` | RJBuilder layout suggestions |
| `AiCodeReviewerService` | Plugin IDE code review |
| `AiCommentService` | Comment moderation |
| `AiSeoService` | SEO meta, OG, Twitter, Schema |
| `AiTranslatorService` | Content translation |
| `AiHealthService` | AI health diagnostics |

## 5. RAG Knowledge Base

- Tables: `rj_knowledge` and `rj_knowledge_translations`
- Currently seeded with 13 entries (26 translations)
- Managed via `KnowledgeSeeder`
- Manual MD files (like this one) can be ingested into `rj_knowledge`
- Dashboard chat queries the knowledge base first, then the AI

**Purpose:** prevent AI hallucination by grounding responses in canonical project knowledge.

## 6. AI Capabilities (all implemented)

### 6.1 Content Generation

- Generate articles from scratch
- Edit existing content
- Rewrite, expand, condense

### 6.2 Translation

- AZ, EN, RU, TR (built-in)
- Unlimited dynamic locales
- Per-entity translation (posts, pages, categories, tags, terms)
- Translation UI in admin

### 6.3 SEO

- Meta title and description generation
- Open Graph generation
- Twitter card generation
- Schema.org structured data
- SEO analysis per content item

### 6.4 Layout Suggestions

- AI suggests layouts in RJBuilder
- Content placement recommendations
- Via `AiBuilderService`

### 6.5 Code Review

- Built into the Plugin IDE
- Reviews, debugs and optimizes code
- Via `AiCodeReviewerService`

### 6.6 Comment Moderation

- AI flags spam and toxic comments
- Via `AiCommentService`

## 7. Data Tables

| Table | Purpose |
|---|---|
| `rj_ai_logs` | AI call history (tokens, cost, model, duration) |
| `rj_ai_prompts` | Reusable prompt library |
| `rj_knowledge` | RAG knowledge base |
| `rj_knowledge_translations` | RAG translations |

## 8. Configuration

Settings, AI tab:

- OpenRouter API key
- Default model
- Fallback provider toggle
- Token limits
- Temperature
- Cost tracking toggle

## 9. AI Health

`AiHealthService` provides a diagnostics endpoint that checks:

- API key validity
- Provider reachability
- Model availability
- Fallback status

Accessible via Tools, Health.

## 10. RAG Integration with Manual MD Files

Manual MD files (`project-overview.md`, `rjbuilder.md`, etc.) are the canonical source of truth. They can be:

1. Pasted into AI chat sessions directly
2. Ingested into `rj_knowledge` via `KnowledgeSeeder` or the admin UI
3. Regenerated as `storage/app/ai-context.md` via `php artisan rj:context`

**Rule:** when the AI answers questions about RJ NEXUS, it MUST reference these MD files and not hallucinate.

## 11. AI Assistant Response Rules

For any AI assistant answering about RJ NEXUS:

**Do:**

- Ground answers in `rj_knowledge` and the manual MD files
- Assume all features in `project-overview.md` are implemented
- Use correct file paths from `database-schema.md`
- Reference `ai-system.md` for AI-specific questions

**Do not:**

- Invent features that are not listed
- Claim something is missing if it is listed as implemented
