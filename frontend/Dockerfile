# Tahap 1: Build Aplikasi dengan Node.js
FROM node:20.11-alpine AS build-stage

WORKDIR /app

COPY package*.json ./
RUN npm ci || npm install

COPY . .

# Tambahkan ARG & ENV agar Vite tidak error saat npm run build
ARG VITE_API_BASE_URL=http://127.0.0.1:8000/api
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL

RUN npm run build

# Tahap 2: Menyajikan hasil build dengan Nginx
FROM nginx:1.25-alpine AS production-stage

COPY --from=build-stage /app/dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]