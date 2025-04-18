# Banking System Application

A robust Banking System built with Laravel (backend) and Vue.js with Tailwind CSS (frontend). This system implements user authentication with two-factor authentication (2FA), account opening with an initial balance, listing accounts, fund transfers including multi-currency conversion with a 0.01 spread on conversions, and transaction history tracking.

## Technologies Used
- **Backend:** Laravel, PHP (with strict type hints and SOLID design)
- **Frontend:** Vue.js, Tailwind CSS
- **Testing:** PHPUnit (unit & integration tests)
- **Static Analysis:** PHPStan
- **Containerization:** Docker, Docker Compose
- **External API:** [Exchange Rates API](https://exchangeratesapi.io/)
- **Version Control:** Git

## Features
- **Authentication:** Registration, login, and 2FA (middleware implemented).
- **Account Management:** Admin can create saving accounts with automatically generated unique account numbers and a default balance.
- **Transaction History:** Track all debits and credits with timestamps and descriptions.
- **Fund Transfers:** Inter-user transfers with multi-currency support.
- **Clean Code:** Usage of dependency injection, small interfaces, separation/encapsulation of logic, and adherence to SOLID principles.

## Getting Started

### Prerequisites

- PHP 8.3
- Laravel 12

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/emekambachu/banking-system-mnk.git
