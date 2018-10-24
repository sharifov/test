FROM nginx:stable-alpine
MAINTAINER f1banac1 "da@zeit.style"
WORKDIR /etc/nginx
COPY conf/nginx.conf /etc/nginx
COPY conf/d-sales.dev.travelinsides.com.conf /etc/nginx/sites-enabled/d-sales.dev.travelinsides.com.conf
COPY conf/d-api-sales.dev.travelinsides.com.conf /etc/nginx/sites-enabled/d-api-sales.dev.travelinsides.com.conf
EXPOSE 80 443
CMD ["nginx", "-g", "daemon off;"]
