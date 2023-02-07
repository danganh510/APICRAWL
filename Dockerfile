FROM willfarrell/crontab

RUN apk add --no-cache logrotate
RUN echo "* * * * *  curl https://reqbin.com/echo" >> /etc/crontabs/logrotate
COPY logrotate.conf /etc/logrotate.conf

CMD ["crond", "-f"]