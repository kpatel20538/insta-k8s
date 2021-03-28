# Insta
A mini Instagram clone showcasing ionic mobile app backed by a php api.

Built with Okteto, A Kubernetes Service Provider.

[![Develop on Okteto](https://okteto.com/develop-okteto.svg)](https://cloud.okteto.com/deploy)

## Infrastrucure Components
 - [x] **api**: [php:7.4-fpm-alpine](https://hub.docker.com/_/php) + [nginx:1.19-alpine](https://hub.docker.com/_/nginx) 
 - [x] **database**: [mysql:8.0](https://hub.docker.com/_/mysql)
 - [x] **frontend**: [node:14.16-alpine](https://hub.docker.com/_/node) + [nginx:1.19-alpine](https://hub.docker.com/_/nginx)
 - [x] **session**: [redis:6.2-alpine](https://hub.docker.com/_/redis)
 - [ ] **worker**: [php:7.4-cli-alpine](https://hub.docker.com/_/php) + [schickling/beanstalkd](https://hub.docker.com/r/schickling/beanstalkd) (Worker + Work Queue)
 - [x] **storage**: [minio/minio](https://hub.docker.com/r/minio/minio) (S3-like Storage)
 - [ ] **analytics**: [matomo:4.2-fpm-alpine](https://hub.docker.com/_/matomo) + [nginx:1.19-alpine](https://hub.docker.com/_/nginx) (Google Analytics-like Tracker)

## Goals
### User Management

Authenication & Authorization was governed by OAuth2 Standard

For the backend, Uses [league/oauth2-server](https://oauth2.thephpleague.com/) as OAuth2 Sever implmentation to facilate mobile login. 

For the mobile app, Ionic Capacitor lacks a well-supported OAuth2 Client, but has tools to make one built-in into [@capacitor/core](https://capacitorjs.com/docs/v3), namely the plugins [Browser](https://capacitorjs.com/docs/apis/browser) to initate the authoirzation code PKCE grant, and [App](https://capacitorjs.com/docs/apis/app) to intercept browser redirects.

Verification Codes for registration and password resest are dispatch over email with [sendgrid/sendgrid](https://github.com/sendgrid/sendgrid-php)

### Background Workers



### Push Notifications



### Image Manipulation



### Analytics Tracking


