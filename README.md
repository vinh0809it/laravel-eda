<h1 align="center">
  Laravel API with Event-Driven Architecture (EDA) & Domain-Driven Design (DDD)
</h1>

<p align="center">
    <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-13-FF2D20.svg?style=flat&logo=laravel" alt="Laravel 13"/></a>
    <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-8.5-777BB4.svg?style=flat&logo=php" alt="PHP 8.5"/></a>
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

## ⭐ Feedback & Contribution

I'm eager to learn and improve!  
If you have suggestions, spot issues, or want to discuss architecture, please open an issue or contact me.

Email: vinh0809it@gmail.com  
Facebook: [vinh0809it](https://www.facebook.com/vinh0809it/)

If you find this project useful, please give it a star ⭐ or leave a comment!

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

The codebase is a **modular monolith**: every bounded context is a module with its own
Domain, Application, Infrastructure and Presentation layers. `Shared` is the shared kernel.

```
src/
├── Shared/                 shared kernel: buses, event store, base classes, API error envelope
│   ├── Domain/             AggregateRoot, DomainEvent, exceptions
│   ├── Application/        Bus (CommandBus, QueryBus, Command, Query...), Ports (AggregateRepository, EventStore, EventMapper, EventProcessLogger)
│   ├── Infrastructure/     Bus/Sync*Bus, EventSourcing/EventSourcedAggregateRepository, Persistence/Eloquent/*, ModuleServiceProvider
│   └── Presentation/       ApiExceptionRenderer, base Controller, Inertia middleware
├── Booking/
│   ├── Domain/             BookingAggregate, domain events, exceptions
│   ├── Application/        Ports (BookingReadRepository, CarCatalog, BookingPricing), UseCases/<UseCase>/{Command|Query, Handler}, DTOs, Listeners
│   ├── Infrastructure/     Persistence/Eloquent (BookingReadModel, projector, read repository), Adapters to Car and Pricing, migrations, provider
│   └── Presentation/       HTTP controller, FormRequests, BookingResource, routes/api.php
├── Car/
│   ├── Contracts/          CarCatalog + CarSnapshot: the only things other modules may use
│   ├── Application/        ReadModelCarCatalog, ports, listeners reacting to Booking events
│   ├── Domain/             events, exceptions
│   └── Infrastructure/     Persistence/Eloquent (CarModel, CarProjector, read repository), Pricing adapter, migrations, provider
├── Pricing/
│   ├── Contracts/          PriceService
│   ├── Domain/             Price value object, PriceCalculator, DefaultPriceService
│   └── Infrastructure/     PricingServiceProvider
└── Identity/
    ├── Infrastructure/     User model, migrations, IdentityServiceProvider
    └── Presentation/       API auth controller, Breeze web controllers, routes/{api,web}.php

tests/
├── Architecture/           layer rules + module boundary rules (Pest arch)
├── Booking/{Unit,Feature}
├── Car/Unit, Pricing/Unit, Shared/Unit
└── Identity/Feature
```

**Module boundaries** (enforced by `tests/Architecture/ModuleBoundariesTest.php`):

1. A module may use another module only through `Src\<Module>\Contracts` (and, until
   integration events land, `Src\<Module>\Domain\Events`). Everything else is private.
2. A module that needs another module declares its own port in `Application/Ports` and
   implements it with an adapter in `Infrastructure/Adapters` (anti-corruption layer).
   Example: `Booking\Application\Ports\CarCatalog` ← `Booking\Infrastructure\Adapters\CarCatalogAdapter` → `Car\Contracts\CarCatalog`.

**Layer rules** (enforced by `tests/Architecture/LayersTest.php`): inside a module dependencies
point inwards only, Domain never imports the framework, use cases are `final readonly`.

**Wiring:** each module has one `ModuleServiceProvider` that declares its command/query handlers,
event listeners, event subscribers (projectors), persisted events, routes and migrations. There is no
central registry. A projector declares the events it reacts to in its own `subscribe()` method.

**Naming:** interfaces carry the role (`BookingReadRepository`, `CommandBus`), implementations carry the
technology (`EloquentBookingReadRepository`, `SyncCommandBus`). Eloquent classes live under
`Infrastructure/Persistence/Eloquent`; a Doctrine implementation would sit next to it.

**Request Flow:**

```
Route (module routes/api.php) → Controller → FormRequest
      → Command / Query (final readonly)
      → CommandBus / QueryBus (handlers resolved lazily from the container)
      → Handler → Aggregate (loaded and saved through the Event Store)
      → Domain Event → Projectors (read models) + Listeners (side effects), own module and subscribed modules
      → BookingDTO → BookingResource (JSON)
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
- **Architecture tests** (`tests/Architecture`) that enforce the dependency direction between layers; CI runs the whole suite on PHP 8.5

---

## 📚 Further Reading

- [Event Sourcing](https://martinfowler.com/eaaDev/EventSourcing.html)
- [Domain-Driven Design Reference](https://domainlanguage.com/ddd/reference/)
- [Clean Architecture](https://8thlight.com/blog/uncle-bob/2012/08/13/the-clean-architecture.html)

---

**Thank you for checking out this project!**  
Let's build better systems, one event at a time.
