# example-wallet-event-microservices

exampe of a small scale digital wallet using microservice architecture

There are three parts

The wallet service - this is the service that holds the digital wallet data and handles all operations and creations of wallets, made with Lumen Framework. 

The main - this is like the user entry point of the system, the main can ask for creation, debiting or crediting a user from the wallet microservice, made with NodeJs.

The notify - this service handles the notifying (sending email) to users mails when actions is carried out, this was written in GoLang.

all these services communicate via a message queue, which i used RabbitMQ, Docker was also used to containerise the application and command ran with supervisord.

test endpoints link : https://www.getpostman.com/collections/4f341db9db5acf10e139
