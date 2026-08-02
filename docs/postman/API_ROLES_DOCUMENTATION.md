# API Role Documentation
> **Project:** Ibraheemaltamim
> **Base URL:** `https://ca2e-103-43-151-193.ngrok-free.app/api`
> **Production n8n Webhook:** `https://svasyndicate.app.n8n.cloud/webhook/ai-assessment-trigger`

---

## 👥 Roles Overview

| Role | Middleware | Prefix | Description |
|------|-----------|--------|-------------|
| **Public** | None | — | No auth needed |
| **User** | `auth:api` + `check_is_user` | — | Regular user |
| **Researcher** | `auth:api` + `check_is_researcher` | `/researcher` | Research applicant |
| **Mentor** | `auth:api` + `check_is_mentor` | `/mentor` | Mentor/supervisor |
| **Coordinator** | `auth:api` + `check_is_coordinator` | `/coordinator` | System coordinator |
| **N8N** | `api` (no JWT) | `/n8n` | Internal webhook only |

---

## 🔐 Auth Endpoints (Guest Only)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/register` | Register new user |
| `POST` | `/api/verify-email` | Verify email with OTP |
| `POST` | `/api/resend-otp` | Resend OTP |
| `POST` | `/api/login` | Login → returns `access_token` |
| `POST` | `/api/forget-password` | Send reset OTP |
| `POST` | `/api/verify-otp` | Verify reset OTP |
| `POST` | `/api/reset-password` | Reset password |
| `POST` | `/api/social-login` | Google/Social login |

## 🔒 Auth Endpoints (Authenticated — All Roles)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/refresh-token` | Refresh JWT token |
| `POST` | `/api/logout` | Logout |
| `GET` | `/api/me` | Get current user profile |
| `POST` | `/api/update-profile` | Update profile info |
| `POST` | `/api/update-password` | Change password |
| `DELETE` | `/api/delete-profile` | Delete own account |

---

## 👤 User Role (`check_is_user`)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/dashboard-data/home` | Home dashboard data |
| `GET` | `/api/my-notifications` | List notifications |
| `POST` | `/api/my-notifications/mark-as-read` | Mark all as read |
| `DELETE` | `/api/my-notifications-delete/{id}` | Delete one notification |
| `DELETE` | `/api/my-notifications-delete-all` | Delete all notifications |
| `GET` | `/api/my-courses` | List enrolled courses |
| `POST` | `/api/courses/{courseId}/buy` | Buy a course |
| `POST` | `/api/payments` | Create payment |
| `POST` | `/api/payments/pay` | Pay |
| `GET` | `/api/payments/{paymentId}` | Get payment details |
| `GET` | `/api/payments` | List payments |
| `POST` | `/api/payments/{paymentId}/verify` | Verify payment |

---

## 🔬 Researcher Role (`check_is_researcher`) — prefix: `/researcher`

### Dashboard
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/researcher/dashboard/overview` | Researcher dashboard |

### Opportunity Applications
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/researcher/opportunity-applications` | List my applications |
| `POST` | `/api/researcher/opportunity-applications` | Apply for opportunity |
| `GET` | `/api/researcher/opportunity-applications/{id}` | Show application |
| `PUT` | `/api/researcher/opportunity-applications/{id}` | Update application |
| `DELETE` | `/api/researcher/opportunity-applications/{id}` | Delete application |

### Favourites
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/researcher/favourites` | My favourite opportunities |
| `POST` | `/api/researcher/favourites` | Add to favourites |
| `GET` | `/api/researcher/favourites/{id}` | Show favourite |
| `DELETE` | `/api/researcher/favourites/{id}` | Remove from favourites |

### Chats
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/researcher/chats` | List chats |
| `GET` | `/api/researcher/chats/{id}` | Show chat |
| `POST` | `/api/researcher/chats/{id}/messages` | Send message |

### Tasks & Submissions (AI Assessment)
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/researcher/tasks` | My assigned tasks |
| `POST` | `/api/researcher/submissions` | Submit task → **triggers n8n AI assessment** |
| `GET` | `/api/researcher/submissions/{id}/feedback` | View AI feedback |

> **⚡ Flow:** Researcher submits → Laravel stores → n8n webhook triggered → GPT-4o assesses → Feedback saved → Researcher views result

---

## 🧑‍🏫 Mentor Role (`check_is_mentor`) — prefix: `/mentor`

### Dashboard
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/mentor/dashboard/overview` | Mentor dashboard |

### Research Ideas
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/mentor/research-ideas` | List research ideas |
| `POST` | `/api/mentor/research-ideas` | Create idea |
| `GET` | `/api/mentor/research-ideas/{id}` | Show idea |
| `PUT` | `/api/mentor/research-ideas/{id}` | Update idea |
| `POST` | `/api/mentor/research-ideas/{id}/submission-proof` | Upload proof |

### Chats
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/mentor/chats` | List chats |
| `GET` | `/api/mentor/chats/{id}` | Show chat |
| `POST` | `/api/mentor/chats/{id}/messages` | Send message |

### Rubrics
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/mentor/rubrics` | List rubrics |
| `POST` | `/api/mentor/rubrics` | Create rubric (with criteria) |
| `GET` | `/api/mentor/rubrics/{id}` | Show rubric |
| `PUT` | `/api/mentor/rubrics/{id}` | Update rubric |
| `DELETE` | `/api/mentor/rubrics/{id}` | Delete rubric |

### Tasks & Submissions
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/mentor/tasks` | List tasks |
| `POST` | `/api/mentor/tasks` | Create task |
| `GET` | `/api/mentor/tasks/{id}` | Show task |
| `PUT` | `/api/mentor/tasks/{id}` | Update task |
| `DELETE` | `/api/mentor/tasks/{id}` | Delete task |
| `GET` | `/api/mentor/submissions` | List all submissions |
| `GET` | `/api/mentor/submissions/{id}/feedback` | View AI feedback |
| `PATCH` | `/api/mentor/feedback/{id}/approve` | Approve AI feedback ✅ |
| `PATCH` | `/api/mentor/feedback/{id}/reject` | Reject AI feedback ❌ |
| `GET` | `/api/mentor/analytics/group/{id}` | Group analytics |

---

## 🗂️ Coordinator Role (`check_is_coordinator`) — prefix: `/coordinator`

### Dashboard & Overview
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/coordinator/dashboard/overview` | System overview |
| `GET` | `/api/coordinator/groups` | All research groups |
| `GET` | `/api/coordinator/tasks` | All tasks (system-wide) |
| `GET` | `/api/coordinator/analytics/group/{id}` | Group analytics |
| `GET` | `/api/coordinator/analytics/export` | Export all analytics |

### Opportunities
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/coordinator/opportunities` | List opportunities |
| `GET` | `/api/coordinator/opportunities/{id}` | Show opportunity |

### Applications
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/coordinator/opportunity-applications` | All applications |
| `GET` | `/api/coordinator/opportunity-applications/{id}` | Show application |

### Chats
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/coordinator/chats` | List chats |
| `GET` | `/api/coordinator/chats/{id}` | Show chat |
| `POST` | `/api/coordinator/chats/{id}/messages` | Send message |

### Publications
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/coordinator/publications` | List publications |
| `GET` | `/api/coordinator/publications/{id}` | Show publication |

### System Rubrics (Global)
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/coordinator/rubrics` | List global rubrics |
| `POST` | `/api/coordinator/rubrics` | Create global rubric |
| `GET` | `/api/coordinator/rubrics/{id}` | Show rubric |
| `PUT` | `/api/coordinator/rubrics/{id}` | Update rubric |
| `DELETE` | `/api/coordinator/rubrics/{id}` | Delete rubric |

### Settings
| Method | Endpoint | Description |
|--------|----------|-------------|
| `PUT` | `/api/coordinator/settings/n8n` | Update n8n webhook URL |

---

## 🌐 Public Endpoints (No Auth)

### Opportunities
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/opportunities` | List all opportunities |
| `GET` | `/api/opportunities/{id}` | Show opportunity |
| `GET` | `/api/opportunities/mentors/{id}` | Opportunity's mentors |
| `POST` | `/api/opportunities/fillter-list` | Filter opportunities |

### Courses
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/courses` | List courses |
| `GET` | `/api/courses/{id}` | Show course |

### Content
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/publications` | List publications |
| `GET` | `/api/publications/{id}` | Show publication |
| `GET` | `/api/services` | List services |
| `GET` | `/api/services/{id}` | Show service |
| `GET` | `/api/offers` | List offers |
| `GET` | `/api/faqs` | List FAQs |
| `GET` | `/api/faqs-categories` | FAQ categories |
| `GET` | `/api/faqs-categories/{id}` | Show category |
| `GET` | `/api/social-links` | Social media links |
| `GET` | `/api/system-info` | System info |
| `GET` | `/api/calendly/schedule-link` | Calendly booking link |

### Actions
| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/partnership-requests` | Send partnership request |
| `POST` | `/api/newsletter/subscribe` | Subscribe newsletter |
| `POST` | `/api/contact-support-message/sent` | Contact support |
| `POST` | `/api/payments/webhook/moyasar` | Moyasar payment webhook |

### CMS Pages
| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/cms/home-page/hero-section` | Home hero |
| `GET` | `/api/cms/home-page/counter-section` | Home counter |
| `GET` | `/api/cms/home-page/why-researchers-choose-section` | Why choose section |
| `GET` | `/api/cms/home-page/featured-research-opportunities-section` | Featured opportunities |
| `GET` | `/api/cms/home-page/meta-academy-section` | Meta academy |
| `GET` | `/api/cms/home-page/review-section` | Reviews |
| `GET` | `/api/cms/home-page/research-journey-section` | Research journey |
| `GET` | `/api/cms/home-page/faq-section` | FAQ section |
| `GET` | `/api/dynamic-pages` | All dynamic pages |
| `GET` | `/api/dynamic-pages/single/{slug}` | Single dynamic page |
| `GET` | `/api/cms/aboutus-page/full-page` | About us full page |

---

## 🤖 N8N Internal Webhooks (No JWT — Internal Only)
> Used by n8n Cloud to communicate with Laravel

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/n8n/tasks/{id}` | Get task details |
| `GET` | `/api/n8n/tasks/{id}/rubric` | Get task rubric |
| `GET` | `/api/n8n/groups/{id}/researchers` | Get group researchers |
| `GET` | `/api/n8n/videos/{id}` | Get task video |
| `POST` | `/api/n8n/task-assignments` | Record task assignment |
| `GET` | `/api/n8n/submissions/{id}` | Get submission for AI |
| `POST` | `/api/n8n/ai-feedback` | **Save AI assessment feedback** |

---

## 🔄 AI Assessment Flow

```
Researcher
    │
    ├─ POST /api/researcher/submissions
    │       { task_id, content_text }
    │
    ▼
Laravel (stores submission)
    │
    ├─ Triggers n8n webhook →
    │   https://svasyndicate.app.n8n.cloud/webhook/ai-assessment-trigger
    │       { submission_id }
    │
    ▼
n8n Cloud
    │
    ├─ GET /api/n8n/submissions/{id}   ← Fetch submission + rubric
    ├─ GPT-4o AI Assessment
    └─ POST /api/n8n/ai-feedback       ← Save result
            { submission_id, text: "{JSON}" }
    │
    ▼
Laravel (saves feedback to DB)
    │
    ▼
Mentor
    ├─ GET /api/mentor/submissions/{id}/feedback  ← View result
    ├─ PATCH /api/mentor/feedback/{id}/approve    ← Approve ✅
    └─ PATCH /api/mentor/feedback/{id}/reject     ← Reject ❌
    │
    ▼
Researcher
    └─ GET /api/researcher/submissions/{id}/feedback ← View final feedback
```

---

## 📦 Postman Collection

Import file: `docs/postman/api_collection.json`

**Collection Variables:**
- `base_url` → Set to your ngrok URL (update when ngrok restarts)
- `user_token` → Auto-saved after User login
- `researcher_token` → Auto-saved after Researcher login
- `mentor_token` → Auto-saved after Mentor login
- `coordinator_token` → Auto-saved after Coordinator login

> ⚠️ **Note:** ngrok URL changes every session. Update `base_url` in collection variables after restarting ngrok.
