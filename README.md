


# Translations API

This project provides an API for translation services, supporting multiple languages. It allows users to translate text from one language to another via a simple RESTful interface.

## Features

* **Multiple Language Support**: Translate text between various languages.
* **Simple REST API**: Easy to integrate into any application.
* **Fast and Reliable**: Optimized for performance.

## API Endpoints & Postman Collection Link:

postman api collection 
https://www.postman.com/avionics-pilot-3898552/translation/collection/k8g5we1/api?action=share&creator=43969890

### `POST /translate`

This endpoint translates the given text from one language to another.

**Request Body**:

```json
{
  "source_language": "en",
  "target_language": "es",
  "text": "Hello, how are you?"
}
```

**Response**:

```json
{
  "translated_text": "Hola, ¿cómo estás?"
}
```

### Parameters

* `source_language`: The language of the input text (ISO 639-1 code, e.g., "en" for English, "es" for Spanish).
* `target_language`: The language to translate to (ISO 639-1 code).
* `text`: The text to be translated.

### `GET /languages`

This endpoint returns a list of supported languages.

**Response**:

```json
{
  "languages": [
    "en",
    "es",
    "fr",
    "de",
    "it",
    "pt"
  ]
}
```

## Installation

### Prerequisites

* Node.js >= 12.x
* npm >= 6.x

### Steps

1. Clone the repository:

   ```bash
   git clone https://github.com/lemukarram/translationsApi.git
   ```

2. Navigate to the project directory:

   ```bash
   cd translationsApi
   ```

3. Install dependencies:

   ```bash
   npm install
   ```

4. Start the server:

   ```bash
   npm start
   ```

The API will be running at `http://localhost:3000`.

## Configuration

You can configure the API settings by modifying the `config.js` file, where you can set parameters such as supported languages, translation providers, etc.

## Usage

* Use `POST /translate` to translate text.
* Use `GET /languages` to fetch the list of supported languages.

## Example Usage

You can test the API using `curl` or Postman.

### Example `curl` command:

```bash
curl -X POST http://localhost:3000/translate \
  -H "Content-Type: application/json" \
  -d '{"source_language":"en","target_language":"es","text":"Hello, how are you?"}'
```

### Example Output:

```json
{
  "translated_text": "Hola, ¿cómo estás?"
}
```

## Contributing

Feel free to fork the repository and submit pull requests. All contributions are welcome!

### Steps for contributing:

1. Fork this repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes.
4. Commit your changes (`git commit -am 'Add new feature'`).
5. Push to the branch (`git push origin feature-branch`).
6. Create a new Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

Let me know if you'd like me to modify anything specific or add additional details!

