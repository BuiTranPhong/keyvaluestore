FROM nginx:alpine
COPY ./src/public ./application/public
#COPY ./certs /etc/nginx/certs
COPY ./config/prod.default.conf /etc/nginx/conf.d/default.conf
#ADD ./config/gzip.conf /etc/nginx/conf.d
#ADD ./config/nginx.conf /etc/nginx/nginx.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]