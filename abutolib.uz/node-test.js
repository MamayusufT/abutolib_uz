// node-test.js
import fetch from 'node-fetch';
import FormData from 'form-data';
import fs from 'fs';

const formData = new FormData();
formData.append("pdf_file", fs.createReadStream("./xxxdzrh.pdf"), "xxxdzrh.pdf");
formData.append("start_page", "1");
formData.append("end_page", "1");
formData.append("language", "uz");
formData.append("difficulty", "o'rta");
formData.append("questions_count", "15");

const requestOptions = {
  method: "POST",
  body: formData,
  headers: formData.getHeaders()
};

fetch("https://ai.abutolib.uz/api/quiz/generate/", requestOptions)
  .then((response) => response.json())
  .then((result) => console.log(result))
  .catch((error) => console.error(error));