<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>API Tester</title>
  <style>
    body { font-family: sans-serif; margin: 2rem; }
    label { display: block; margin-top: 1rem; }
    input, select, textarea { width: 100%; padding: .5rem; margin-top: .25rem; }
    button { margin-top: 1rem; padding: .5rem 1rem; }
    pre { background: #f4f4f4; padding: 1rem; white-space: pre-wrap; }
  </style>
</head>
<body>
  <h1>Project API Tester</h1>

  <button id="send">Send Request</button>

  <a href="/test/projects/create"><h2>create project</h2></a>

  <h2>Response</h2>
  <pre id="response">—</pre>

  <script>
    document.getElementById('send').addEventListener('click', async () => {
      const token = localStorage.getItem('auth_token');
      let method = 'get';
      let options = { method, headers: {} };

      if (token) {
        options.headers['Authorization'] = 'Bearer ' + token;
      }

        /*try {
          options.headers['Content-Type'] = 'application/json';
          options.body = JSON.stringify(JSON.parse(body));
        } catch (e) {
          return alert('Invalid JSON body');
        }*/

      try {
        const res = await fetch(`/api/projects`);
        const text = await res.text();
        let json;
        try { json = JSON.stringify(JSON.parse(text), null, 2); }
        catch { json = text; }
        document.getElementById('response').textContent =
          `HTTP ${res.status} ${res.statusText}\n\n` + json;
      } catch (err) {
        document.getElementById('response').textContent = 'Error: ' + err;
      }
    });
  </script>
</body>
</html>
