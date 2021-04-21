package main

import (
	"fmt"
	"html"
	"log"
	"net/http"
)

func main() {

	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "Notify Service, %q", html.EscapeString(r.URL.Path))
	})

	fmt.Println("started notify service")

	log.Fatal(http.ListenAndServe(":8081", nil))
}
