CRM: Infrastructure as Code
-------------------------------------------

![](docs/imgs/infrastructure.png)

CRM application is written on PHP 7.4 and uses Yii 2.0 framework underneath.
It allows us to manage company's customers interactions.

CRM application exposes 4 entry-points:
    - App that provides user-interface
    - API for internal and external services
    - Websocket connection for instant messaging
    - Websocket connection for system notifications

Logically you can split CRM architecture into the following layers
    - Application layer consist of Ubuntu servers that run Nginx, php-fpm and supervisord
    - Websockets servers are implemented using swoole and centrifugo
    - Caching layer is handled by Redis
    - Queue layer is managed by Beanstalk
    - Persistent data layer consists of RDS MySQL, PostgreSQL and S3

# [Terraform](terraform/dev/README.md)
In this directory, you will find manifests that describe AWS infrastructure.

# [Ansible](ansible/README.md)
This directory contains playbooks that are used in server provisioning and application deployment.

# [Jenkins](jenkins/README.md)
Jenkins dir contains CI/CD pipelines that interact with ansible playbooks.

# [Bash](bash/README.md)
This directory contains handy script that can assist you with env migration.


Contact Us
==========

For more information please contact: [devops@techork.com](mailto:devops@techork.com).
You can also join our [channel](https://chat.travel-dev.com/devoffice/channels/devops).
