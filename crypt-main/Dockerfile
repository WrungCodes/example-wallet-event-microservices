FROM node:10

WORKDIR /usr/src/app

COPY package.json ./

RUN npm install

RUN npm install -g --save concurrently

RUN apt-get update -y && \
  apt-get upgrade -y --force-yes && \
  apt-get install -y --force-yes supervisor 

COPY . .

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 5000

# CMD ["npm", "run", "server" ]

CMD ["/usr/bin/supervisord"]
