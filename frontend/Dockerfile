# Tahap 1: Build Aplikasi dengan Node.js (Multi-stage Build)
FROM node:20.11-alpine AS build-stage

WORKDIR /app

# Salin file dependensi dulu untuk Layer Caching
COPY package*.json ./
RUN npm ci

# Salin source code dan jalankan build
COPY . .
RUN npm run build

# Tahap 2: Menyajikan hasil build dengan Nginx (Tanpa node_modules)
FROM nginx:1.25-alpine AS production-stage

# Salin HANYA folder dist/ hasil build dari Tahap 1 ke direktori Nginx
COPY --from=build-stage /app/dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]