FROM node:14.16-alpine AS build

WORKDIR /app
COPY ./package.json ./package-lock.json ./
RUN npm ci
COPY . ./
RUN npm run build

FROM nginx:1.19-alpine AS prod

COPY ./nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/build/. /var/www/html
