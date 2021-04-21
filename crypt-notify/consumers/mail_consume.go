package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/smtp"
	"os"

	"github.com/streadway/amqp"
)

func checkFailOnError(err error, msg string) {
	if err != nil {
		log.Fatalf("%s: %s", msg, err)
		return
	}
}

func main() {
	user := os.Getenv("RABBITMQ_USER")
	password := os.Getenv("RABBITMQ_PASSWORD")
	host := os.Getenv("RABBITMQ_HOST")
	port := os.Getenv("RABBITMQ_PORT")
	vhost := os.Getenv("RABBITMQ_VHOST")

	emailQueue := os.Getenv("RABBITMQ_EMAIL_QUEUE")

	conString := fmt.Sprintf("amqp://%s:%s@%s:%s%s", user, password, host, port, vhost)

	conn, err := amqp.Dial(conString)

	checkFailOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	checkFailOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		emailQueue, // name
		true,       // durable
		false,      // delete when unused
		false,      // exclusive
		false,      // no-wait
		nil,        // arguments
	)
	checkFailOnError(err, "Failed to declare a queue")

	msgs, err := ch.Consume(
		q.Name, // queue
		"",     // consumer
		false,  // auto-ack
		false,  // exclusive
		false,  // no-local
		false,  // no-wait
		nil,    // args
	)
	checkFailOnError(err, "Failed to register a consumer")

	forever := make(chan bool)

	go func() {
		for d := range msgs {
			handle(d.Body)
			d.Ack(true)
		}
	}()

	log.Printf(" [*] Waiting for logs. To exit press CTRL+C")
	log.Printf("")
	<-forever
}

func sendMail(email string, message string) {
	from := os.Getenv("MAIL_FROM_ADDRESS")
	username := os.Getenv("MAIL_USERNAME")
	password := os.Getenv("MAIL_PASSWORD")
	smtpHost := os.Getenv("MAIL_HOST")
	smtpPort := os.Getenv("MAIL_PORT")

	auth := smtp.CRAMMD5Auth(username, password)

	if err := smtp.SendMail(smtpHost+":"+smtpPort, auth, from, []string{email}, []byte(message)); err != nil {
		log.Fatal(err)
	}
	fmt.Println("Email Sent!")
}

func handle(message []byte) {

	var result map[string]interface{}
	json.Unmarshal([]byte(message), &result)

	data := result["data"].(map[string]interface{})
	eventMessage := data["event"]

	fmt.Println(eventMessage)
	if eventMessage == "DEBIT_WALLET" {
		info := data["info"].(map[string]interface{})
		email := info["email"].(string)
		sendMail(email, "a debit request has been made on your wallet")
	}

	if eventMessage == "WALLET_DEBITED" {
		wallet := data["wallet"].(map[string]interface{})
		email := wallet["email"].(string)
		sendMail(email, "your debit transaction was successful")
	}

	if eventMessage == "WALLET_DEBIT_FAILED" {
		wallet := data["wallet"].(map[string]interface{})
		email := wallet["email"].(string)
		sendMail(email, "your debit transaction failed")
	}

	if eventMessage == "CREDIT_WALLET" {
		info := data["info"].(map[string]interface{})
		email := info["email"].(string)
		sendMail(email, "a credit request has been made on your wallet")
	}

	if eventMessage == "WALLET_CREDITED" {
		wallet := data["wallet"].(map[string]interface{})
		email := wallet["email"].(string)
		sendMail(email, "your credit transaction was successful")
	}

	if eventMessage == "WALLET_CREDIT_FAILED" {
		wallet := data["wallet"].(map[string]interface{})
		email := wallet["email"].(string)
		sendMail(email, "your credit transaction failed")
	}

	if eventMessage == "CREATE_WALLET" {
		info := data["info"].(map[string]interface{})
		email := info["email"].(string)
		sendMail(email, "your wallet is being created")
	}

	if eventMessage == "WALLET_CREATED" {
		data1 := data["data"].(map[string]interface{})
		wallet := data1["wallet"].(map[string]interface{})
		email := wallet["email"].(string)
		sendMail(email, "your wallet has been created")
	}

	if eventMessage == "WALLET_CREATE_FAILED" {
		info := data["info"].(map[string]interface{})
		email := info["email"].(string)
		sendMail(email, "your wallet creation failed")
	}

	// fmt.Println(data)
}
