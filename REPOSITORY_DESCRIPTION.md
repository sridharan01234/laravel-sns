# SMS Campaign Manager

## Description
SMS Campaign Manager is a comprehensive Laravel-based web application designed for managing and executing SMS marketing campaigns at scale. The application enables businesses to efficiently manage customer contacts, create and schedule SMS campaigns, use message templates, and track message delivery status.

## Key Features
- Campaign Management: Create, schedule, and track SMS marketing campaigns
- Customer Management: Import and organize customer contact information
- Template System: Create and manage reusable message templates
- Bulk Messaging: Send messages to multiple recipients simultaneously
- Group Management: Organize customers into groups for targeted messaging
- Message Tracking: Monitor message delivery status and campaign performance

## Technical Stack
- **Framework**: Laravel 11.x
- **PHP Version**: 8.2+
- **Dependencies**:
  - AWS SDK for PHP (aws/aws-sdk-php)
  - Twilio SDK for SMS capabilities
  - Maatwebsite Excel for data import/export
  - Laravel core packages (Tinker, etc.)

## Architecture
The application follows Laravel's MVC architecture with:
- Models for Campaign, Customer, Message, and Template entities
- Services layer for external SMS provider integration (MSG91)
- Support for bulk operations through Laravel jobs
- Excel import/export functionality for customer data management

## Integration
- SMS Provider Integration through MSG91Service
- AWS services support via AWS SDK
- Alternative SMS gateway support through Twilio

## Development Stack
- Laravel 11 framework
- PHP 8.2+
- Tailwind CSS for styling
- Vite for asset bundling
- PHPUnit for testing

This application is ideal for businesses and organizations needing a robust solution for managing SMS marketing campaigns and customer communications at scale.