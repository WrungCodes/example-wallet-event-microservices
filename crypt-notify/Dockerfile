# FROM golang:1.12.0-alpine3.9
FROM golang:1.16

RUN mkdir /app

WORKDIR /app

RUN apt-get update -y && \
  apt-get upgrade -y --force-yes && \
  apt-get install -y --force-yes supervisor 

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY . .

RUN go mod download

RUN go build -o main .
RUN go build consumers/mail_consume.go

# CMD ["/app/main"]
EXPOSE 8081

CMD ["/usr/bin/supervisord"]