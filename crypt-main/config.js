require('dotenv').config()

const config= {
    port: process.env.APP_PORT,
    rabbit: {
        connectionString: `amqp://${process.env.RABBITMQ_USER}:${process.env.RABBITMQ_PASSWORD}@${process.env.RABBITMQ_HOST}:${process.env.RABBITMQ_PORT}${process.env.RABBITMQ_VHOST}`,
        
        queue: {
            email: 'wallet_action.email',
            main: 'wallet_action.main',
            wallet: 'wallet_action.wallet',
        },

        exchange: {
            wallet_action: 'wallet_action.exchange',
            wallet_action_response: 'wallet_action_response.exchange'
        }
    }
}

module.exports = config;