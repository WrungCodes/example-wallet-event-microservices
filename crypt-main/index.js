const restify = require('restify');
const errs = require('restify-errors');
const axios = require('axios');
const server = restify.createServer({
    name: 'Main App',
    version: '1.0.0'
});
const config = require('./config');
const produce = require('./wallet-producer');
require('dotenv').config()

server.use(restify.plugins.queryParser());
server.use(restify.plugins.bodyParser({
    requestBodyOnGet: true
}));

server.get('/', (req, res, next) => {
  res.send({
    message: 'welcome to ' + server.name
  });
  next();
})
    // "start": "concurrently \"npm run server\" \"npm run wallet:consume\""

server.post('/wallet/create', async (req, res, next) => {
  const wallet = await checkIfEmailExists(req.body.email)

  if(wallet.error)
  {
    res.send(new errs.InternalError('server error'));
  }

  console.log(wallet)
  
  if(wallet === true)
  {
    res.send(new errs.InvalidContentError('email already in use'));
  }

  produce(
    JSON.stringify({
      data: {
        event: "CREATE_WALLET",
        info: {
            email: req.body.email,
            name: req.body.name,
            phone: req.body.phone
        }
      }
    })
  );

  res.send({
    message: 'wallet creation processsing'
  });
  next();
});

server.post('/wallet/credit', (req, res, next) => {
  produce(
    JSON.stringify({
      data: {
        event: "CREDIT_WALLET",
        info: {
          email: req.body.email,
          name: req.body.name,
          phone: req.body.phone,
          amount: req.body.amount
        },
        wallet: {
          identifier: req.body.wallet
        }
      }
    })
  );

  res.send({
    message: 'wallet credit processsing'
  });
  next();
});

server.post('/wallet/debit', (req, res, next) => {
  produce(
    JSON.stringify({ 
      data: {
        event: "DEBIT_WALLET",
        info: {
          email: req.body.email,
          name: req.body.name,
          phone: req.body.phone,
          amount: req.body.amount,
        },
        wallet: {
          identifier: req.body.wallet
        }
      }
      }
    )
  );

  res.send({
    message: 'wallet debit processsing'
  });
  next();
});

server.get('/wallet/:id/:value', async(req, res, next) => {
  
  const data = await sendGetWalletRequest(req.params.id, req.params.value);

  if(data.error)
  {
    if(data.error.response.status == 500)
    {
      res.send(new errs.InternalError('server error'));
    }

    if(data.error.response.status == 400)
    {
      res.send(new errs.InvalidContentError('invalid request'));
    }

    if(data.error.response.status == 404)
    {
      res.send(new errs.ResourceNotFoundError('wallet not found'));
    }

    console.log(data.error)
    res.send(new errs.InternalError('server error'))
  }

  if(data.response)
  {
    res.send(data.response.data);
  }
  next();
});

const sendGetWalletRequest = async (id, value) => {
  try {
      const response = await axios.get(`http://${process.env.WALLET_HOST}:${process.env.WALLET_PORT}/wallet/${id}/${value}`);
      return {response: response}
  } catch (error) {
      return {error: error}
  }
};

const checkIfEmailExists = async (email) => {
  try {
      const response = await axios.get(`http://${process.env.WALLET_HOST}:${process.env.WALLET_PORT}/wallet/email/${email}`);
      return true
  } 
  catch (error) 
  {
    if(error.response.status == 404)
    {
      return false
    }

    return {error: error}
  }
};


server.listen(config.port, function () {
  console.log('%s listening at %s', server.name, server.url);
});