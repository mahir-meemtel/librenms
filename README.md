# Obzora NMS

**Monitor. Visualize. All in one place.**

ObzoraNMS is an autodiscovering PHP/MySQL-based network monitoring system. It's a community-based project designed to monitor network devices, servers, and infrastructure components using SNMP and other protocols.

## Features

- **Auto-Discovery**: Automatically discovers network devices and their components
- **Real-time Monitoring**: Monitor network performance, availability, and health metrics
- **Graphing & Visualization**: Generate graphs and visualizations for network metrics
- **Alerting System**: Comprehensive alerting with multiple transport methods (Email, Slack, Telegram, PagerDuty, and many more)
- **Multi-Protocol Support**: SNMP, ICMP, and other network protocols
- **Device Management**: Support for a wide range of network devices and operating systems
- **Dashboard**: Customizable dashboards with widgets
- **API**: RESTful API for integration with other systems
- **Multi-language Support**: Available in multiple languages (English, German, French, Italian, Russian, Chinese, and more)

## Technology Stack

- **Backend**: PHP (Laravel Framework)
- **Frontend**: Vue.js 2.7, Tailwind CSS, Bootstrap 4
- **Database**: MySQL/MariaDB
- **Data Storage**: RRD (Round Robin Database)
- **Scripting**: Python 3 (for polling, discovery, and services)
- **Build Tools**: Vite, npm

## Requirements

- PHP 7.4+ (with required extensions)
- MySQL/MariaDB
- Python 3.6+
- Node.js and npm (for frontend assets)
- RRDtool
- Net-SNMP
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd obzora
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Build frontend assets:
   ```bash
   npm run build
   ```

5. Configure the application:
   - Copy `config.php.default` to `config.php` and configure your settings
   - Set up your database connection
   - Configure web server to point to the `html` directory

6. Run database migrations:
   ```bash
   php artisan migrate
   ```

7. Set up cron jobs for polling and discovery (see `dist/obzora.cron`)

8. Configure systemd services (optional, see `dist/obzora-scheduler.service`)

## Project Structure

```
.
├── app/                    # Laravel application code
├── ObzoraNMS/             # Core ObzoraNMS classes and modules
├── resources/
│   ├── views/             # Blade templates
│   ├── js/                # Vue.js components
│   └── definitions/       # Device discovery definitions (YAML)
├── html/                  # Web-accessible files
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── images/           # Images and icons
│   └── graph.php         # Graph generation endpoint
├── includes/              # Legacy PHP includes
│   ├── discovery/        # Device discovery modules
│   ├── polling/          # Polling modules
│   └── html/             # HTML generation modules
├── routes/                # Laravel routes
├── database/              # Database migrations and seeds
├── tests/                 # Test files
├── mibs/                  # SNMP MIB files
├── lang/                  # Translation files
└── scripts/              # Utility scripts
```

## Key Components

### Polling System
- **Poller**: Collects data from devices using SNMP and other protocols
- **Discovery**: Automatically discovers devices and their components
- **Services**: Monitors services and applications

### Alerting System
- Multiple alert transport methods (Email, Slack, Telegram, PagerDuty, etc.)
- Alert rules and templates
- Alert scheduling

### Graphing
- RRD-based time-series data storage
- Multiple graph types (bandwidth, errors, availability, etc.)
- Customizable graph widgets for dashboards

## Development

### Running Development Server

```bash
# Frontend development with hot reload
npm run dev

# Watch for changes in production mode
npm run watch-production
```

### Code Quality

The project uses:
- PHPStan for static analysis
- PHPUnit for testing
- ESLint/Prettier for JavaScript (if configured)

### Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## Configuration

Main configuration file: `config.php`

Key configuration areas:
- Database connection
- SNMP settings
- Polling intervals
- Alert settings
- Graph settings
- Base URL configuration

## Documentation

- See `doc/` directory for additional documentation
- Check `mkdocs.yml` for documentation structure
- API documentation available via API endpoints

## License

This project is licensed under the GPLv3 License - see the `licenses/GPLv3-LICENSE.txt` file for details.

## Support

- **Website**: https://www.obzora.net
- **Email**: info@obzora.net
- **Community Forum**: Available on the website

## Acknowledgments

ObzoraNMS is a community-based project. Special thanks to all contributors who have helped make this project possible.

---

**Note**: This is a network monitoring system. Ensure proper security measures are in place when deploying in production environments.

