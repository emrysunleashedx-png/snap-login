FROM php:8.3-cli-alpine
WORKDIR /app
COPY index.php ./
RUN mkdir -p /data
ENV STORE_DIR=/data
EXPOSE 10000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} -t /app"]
