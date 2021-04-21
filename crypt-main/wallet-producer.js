const amqp = require('amqplib');
const config = require('./config');

const pulishToQueue = async (message, durable = true) => {
    try {
        const cluster = await amqp.connect(config.rabbit.connectionString);
        const channel = await cluster.createChannel();

        const exchange = config.rabbit.exchange.wallet_action;
        const queue1 = config.rabbit.queue.wallet
        const queue2 = config.rabbit.queue.email

        channel.assertExchange(exchange, 'fanout', {durable: durable})

        await channel.assertQueue(queue1, durable= true);
        await channel.assertQueue(queue2, durable= true);

        await channel.bindQueue(queue1, exchange, '');
        await channel.bindQueue(queue2, exchange, '');

        await channel.publish(exchange, '', Buffer.from(message));

        console.info(' [x] Sending message to exchange', exchange, JSON.parse(message));
    }
    catch(error)
    {
        console.error(error, 'Unable to connect to cluster!');  
        process.exit(1);
    }
}

module.exports = pulishToQueue;