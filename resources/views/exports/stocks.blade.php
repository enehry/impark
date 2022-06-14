<h1>
  <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/impark_logo.png''))) }}">
</h1>
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Email</th>
    </tr>
  </thead>
  <tbody>
    @foreach($stocks as $stock)
    <tr>
      <td>{{ $stock->name }}</td>
      <td>{{ $stock->price }}</td>
    </tr>
    @endforeach
  </tbody>
</table>