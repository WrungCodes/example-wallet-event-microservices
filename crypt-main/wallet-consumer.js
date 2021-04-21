const amqp = require('amqplib');
const config = require('./config');

const consumeFromQueue = async (prefetch = null) => {
    const cluster = await amqp.connect(config.rabbit.connectionString);
    const channel = await cluster.createChannel();

    const queue = config.rabbit.queue.main
    await channel.assertQueue(queue, durable=true);

    if (prefetch) {
        channel.prefetch(prefetch);
    }

    console.log(` [x] consumer 2 Waiting for messages in ${queue}. To exit press CTRL+C`)

    try {
        channel.consume(queue, message => {
            if (message !== null) {
                console.log(' [x] wallet action received', JSON.parse(message.content.toString()));

                // do some logic
                // notify the user about the response

                channel.ack(message);
                return null;
            } else {
                console.log(error, 'Queue is empty!')
                channel.reject(message);
            }
       }, {noAck: false})
    } catch (error) {
        console.log(error, 'Failed to consume messages from Queue!')
        cluster.close(); 
    }
}

consumeFromQueue();