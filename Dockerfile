FROM sethsandaru/php73-phalcon-laravel-fpm:1.0.0
# Thêm dòng này để bật SSL
ENV ENABLE_SSL=false
# Thêm dòng này để sao chép file cấu hình nginx và bật SSL (nếu ENABLE_SSL=true)
RUN if [ "$ENABLE_SSL" = "true" ] ; then \
        cp /etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf.backup; \
        cp /etc/nginx/ssl/nginx-ssl.conf /etc/nginx/conf.d/default.conf; \
        sed -i 's/listen       80;/listen       443 ssl;/g' /etc/nginx/conf.d/default.conf; \
        sed -i 's/#ssl_/ssl_/g' /etc/nginx/conf.d/default.conf; \
    fi