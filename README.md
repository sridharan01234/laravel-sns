# SMS Campaign Manager

## Overview
SMS Campaign Manager is a robust Laravel-based application designed for managing and executing SMS marketing campaigns at scale. The system enables users to create, schedule, and track SMS campaigns while managing customer groups and message templates efficiently.

## Features
- **Campaign Management**
  - Create and schedule SMS campaigns
  - Duplicate existing campaigns
  - Track campaign status and performance
  - Resend failed messages

- **Customer Management**
  - Organize customers into groups
  - Import/export customer data
  - Track message history per customer

- **Template System**
  - Create reusable message templates
  - Support for variable substitution
  - Template version control

- **Messaging Integration**
  - Integration with MSG91 service
  - Bulk message sending capability
  - Message delivery tracking

## Technology Stack
- PHP 8.2+
- Laravel 11.x
- AWS SDK for PHP
- Twilio SDK
- Maatwebsite Excel for imports/exports

## Installation

1. Clone the repository
```bash
git clone [repository-url]
cd sms-campaign-manager
```

2. Install dependencies
```bash
composer install
npm install
```

3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure Database
- Create a database
- Update .env with database credentials
```bash
php artisan migrate
```

5. Configure SMS Service
Update your .env file with MSG91/Twilio credentials:
```
MSG91_AUTH_KEY=your_auth_key
```

6. Start the Development Server
```bash
php artisan serve
npm run dev
```

## Usage

### Creating a Campaign
1. Navigate to Campaigns section
2. Click "New Campaign"
3. Select target customer groups
4. Choose or create a message template
5. Schedule or send immediately

### Managing Customers
1. Import customers via Excel/CSV
2. Create and manage customer groups
3. View message history per customer

### Templates
1. Create reusable templates
2. Use variables for personalization
3. Test templates before campaign execution

## Contributing
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License
This project is licensed under the MIT License.

## Support
For support and queries, please create an issue in the repository or contact the development team.