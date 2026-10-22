# Tahap 1: Build Aplikasi dengan Node.js
FROM node:20-alpine AS build-stage

WORKDIR /app

COPY package*.json ./

# Paksa npm untuk menginstall seluruh paket (termasuk devDependencies seperti Vite)
RUN npm install --include=dev

COPY . .

# Variabel pendukung untuk Vite build
ARG VITE_API_BASE_URL=http://127.0.0.1:8000/api
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL

# Build frontend
RUN npm run build

# Tahap 2: Web Server Nginx
FROM nginx:alpine AS production-stage
COPY --from=build-stage /app/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]