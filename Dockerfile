FROM alpine:3.12

RUN apk update && apk upgrade
RUN apk add --no-cache tzdata cron

COPY job1 /etc/cron.d/job1


RUN chmod 0644 /etc/cron.d/job1

CMD ["crond", "-f"]