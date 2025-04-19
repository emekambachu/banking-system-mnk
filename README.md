# Banking System Application

A robust Banking System built with Laravel (backend) and Vue.js with Tailwind CSS (frontend). This system implements user authentication with two-factor authentication (2FA), account opening with an initial balance, listing accounts, fund transfers including multi-currency conversion with a 0.01 spread on conversions, and transaction history tracking.

### Prerequisites
- PHP 8.3
- Laravel 12

### Installation
1. **Clone the repository:**
   ```bash
   git clone https://github.com/emekambachu/banking-system-mnk.git
    cd banking-system-mnk
    ```
2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```
3. **Copy the `.env.example` file to `.env`:**
   ```bash
    cp .env.example .env
    ```
4. **Generate user data using UserSeeder or register for the first time to become admin, then create other users:**
   ```bash
   php artisan db:seed --class=UserSeeder
   ```

## Technologies Used
- **Backend:** Laravel, PHP (with type hints, SOLID design, Software Design Patterns and Dependency injection)
- **Frontend:** Vue.js, Tailwind CSS
- **Testing:** PHPUnit (unit & integration tests)
- **Static Analysis:** PHPStan
- **External API:** [Exchange Rates API](https://exchangeratesapi.io/) API Key is sent seperately via email.
- **Version Control:** Git
- **Database:** SQLite

## Features
- **Authentication:** Registration, login, and 2FA (middleware implemented). First user sign-up is automatically assigned as an admin, the rest becomes regular users.
- **User Registration:** Users can register but will not have login access until the admin approves their account. Admin can approve or reject user accounts.
- **Factories and seeders** There are factories and seeders used to generate dummy data. The UserSeeder is the most important one, as it creates an admin, 10 users with random names and emails, with Accounts and 2FA Activation
- **2FA:** 2FA is enabled for all users by default on signup or creation. When a users logs in, they are required to enter a 2FA code sent to their email (Check log file to see 2FA OTP). The code is valid for 5 minutes.
- **User Roles:** Admin and User roles with different permissions. Admin can manage users and accounts, while users can only manage their own accounts.
- **Account Management:** Admin can create saving accounts with automatically generated unique account numbers, approved users and a default balance of 10,000 USD.
- **Account Listing:** Admin can view, search and filter all users and their accounts.
- **Transaction History:** Admin cn see all transactions, while users can only see their own transactions.
- **Fund Transfers:** Users can send funds to any ones in the available currencies USD, GBP, EUR. All transactions and account balances are updated in real-time.
- **Multi-Currency Conversion:** Users can transfer funds in different currencies. The system uses the Exchange Rates API to get the latest exchange rates and converts them, while a 0.01 spread is applies upon conversions.
- **Clean Code:** Usage of dependency injection, Services, Repositories, separation/encapsulation of logic, and adherence to SOLID principles.
- **Testing:** Unit and integration tests for Controllers, Services and Repositories.

## What i would like to add if given more time
- **More tests:** I would like to write more tests to endure application reliability.
- **Notifications:** Implement a notification system to inform users about important events (e.g., successful fund transfers, account approvals).
- **Email Verification:** Implement email verification for user registration.
- **Password Reset:** Implement a password reset feature for users.
- **Containerization:** Containerize the application using Docker for easier deployment.
- **State management for frontend:** Implement Pinia for state management in the Vue.js frontend.
- **Front-end Testing:** Add unit and integration tests for the Vue.js components using Cypress.
- **Security Enhancements:** Implement additional security measures such as rate limiting.
- **Logging and Monitoring:** Implement logging and monitoring to track application performance and errors.
