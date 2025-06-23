<h1 align="center">
  Laravel API with Event-Driven Architecture (EDA) & Domain-Driven Design (DDD)
</h1>

<p align="center">
    <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-10-FF2D20.svg?style=flat&logo=laravel" alt="Laravel 12"/></a>
    <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-8.1-777BB4.svg?style=flat&logo=php" alt="PHP 8.2"/></a>
    <a href="#"><img src="https://github.com/yourusername/yourrepo/actions/workflows/laravel-tests.yml/badge.svg" alt="GithubActions"/></a>
</p>

## 🚀 Current Features

- **Booking API Endpoints:**
  - Get bookings
  - Create booking
  - Change booking
  - Complete booking
  - Cancel booking
- **Booking Domain** with event-sourced aggregate and projections
- **Car Domain** and **Pricing Domain** with clear boundaries
- Event Store and event replay for aggregate state
- Event listeners and projections for read models
- Integration and unit tests for core flows
- Clean separation of Application, Domain, Infrastructure, and Presentation layers

## 📘 Introduction

This project is my exploration of building a robust Laravel API using **Event-Driven Architecture (EDA)**, **Event Sourcing**, and **Domain-Driven Design (DDD)**, all within the context of **Clean Architecture**.  
The goal is to create a scalable, maintainable, and testable foundation for complex business domains—demonstrated here with a booking system.

**Why EDA & Event Sourcing?**  
Event sourcing ensures every state change is captured as an immutable event, providing a complete audit trail and enabling powerful features like event replay, temporal queries, and easy integration with other systems.

**Why DDD?**  
Domain-Driven Design helps keep business logic at the core, with clear boundaries and ubiquitous language, making the codebase easier to reason about and evolve.

**Why Clean Architecture?**  
It enforces separation of concerns, making the system more modular and adaptable to change.

**Feedback is welcome!**  
If you spot any issues, have suggestions, or want to discuss architecture, please open an issue or contact me directly.

Email: vinh0809it@gmail.com  
Facebook: [vinh0809it](https://www.facebook.com/vinh0809it/)

---

## 📗 Installation

1. `composer install`
2. `cp .env.example .env`
3. `php artisan key:generate`
4. Set your DB connection in `.env`
5. `php artisan migrate`
6. `php artisan db:seed`
7. `npm install`
8. `npm run build`
7. `php artisan test`
8. `composer run dev`
9. Import the collection (exported from Isomnia) from the `api-doc` folder for API doc

---

## 📁 Project Structure

This project follows a strict separation of concerns, inspired by Clean Architecture and DDD, with a strong focus on EDA and event sourcing.

```
src/
├── Domain/
│   ├── Booking/
│   ├── Car/
│   ├── Pricing/
│   └── Shared/
├── Application/
│   ├── Booking/
│   ├── Car/
│   ├── Pricing/
│   └── Shared/
├── Infrastructure/
│   ├── Booking/
│   ├── Car/
│   ├── Pricing/
│   ├── EventStore/
│   └── Shared/
├── Presentation/
│   ├── Booking/
│   ├── Car/
│   ├── Pricing/
│   └── Shared/
```

**Layer Responsibilities:**

- **Domain:** Aggregates, entities, value objects, domain services, and events.
- **Application:** Use cases (commands, queries, handlers), DTOs, ...
- **Infrastructure:** Projections, event consumers, repositories, event store, service providers, and integrations.
- **Presentation:** Controllers and request adapters (API layer).

**Request Flow:**

```
Route → Controller
      → Command/Query
      → Command/Query Handler
      → Aggregate (persisted via Event Store)
      → Domain Service (executes business logic)
      → Dispatch Domain Event
      → Projection (updates read models or views)
```

---

## 🧩 Key Concepts

- **Event Sourcing:** All changes to aggregates are stored as a sequence of events. Aggregates are reconstructed by replaying these events.
- **Projections:** Read models are updated by event listeners, ensuring eventual consistency and optimized queries.
- **CQRS:** Commands mutate state via aggregates; queries read from projections.
- **Domain Events:** Encapsulate every significant change in the system, enabling decoupled processing and integration.

---

## 🧪 Testing

- **Unit tests** for aggregates, services, and event logic
- **Feature tests** for API endpoints, use case flows and projection updates

---

## ⭐ Feedback & Contribution

I'm eager to learn and improve!  
If you have suggestions, spot issues, or want to discuss architecture, please open an issue or contact me.

If you find this project useful, please give it a star ⭐ or leave a comment!

---

## 📚 Further Reading

- [Event Sourcing](https://martinfowler.com/eaaDev/EventSourcing.html)
- [Domain-Driven Design Reference](https://domainlanguage.com/ddd/reference/)
- [Clean Architecture](https://8thlight.com/blog/uncle-bob/2012/08/13/the-clean-architecture.html)

---

**Thank you for checking out this project!**  
Let's build better systems, one event at a time.
