<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Tenant: {{ $company->name }}</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 1100px; margin: 32px auto; }
    h1,h2 { margin: 8px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; }
    th { background: #f6f6f6; text-align: left; }
    .meta { margin-bottom: 16px; }
  </style>
</head>
<body>
  <h1>{{ $company->name }} ({{ $company->slug }})</h1>
  <div class="meta">База: <b>{{ $dbName }}</b> | Статус: <b>{{ $company->status }}</b> | <a href="/admin">&larr; назад</a></div>

  <h2>Клиенты (первые 50)</h2>
  <table>
    <thead><tr><th>ID</th><th>Имя</th><th>Email</th><th>Создан</th></tr></thead>
    <tbody>
      @forelse($clients as $x)
        <tr><td>{{ $x->id }}</td><td>{{ $x->name }}</td><td>{{ $x->email }}</td><td>{{ $x->created_at }}</td></tr>
      @empty
        <tr><td colspan="4">Нет данных</td></tr>
      @endforelse
    </tbody>
  </table>

  <h2>Контракты (первые 50)</h2>
  <table>
    <thead><tr><th>ID</th><th>Клиент</th><th>Номер</th><th>Статус</th><th>Подписан</th><th>Создан</th></tr></thead>
    <tbody>
      @forelse($contracts as $x)
        <tr><td>{{ $x->id }}</td><td>{{ $x->client_id }}</td><td>{{ $x->number }}</td><td>{{ $x->status }}</td><td>{{ $x->signed_at }}</td><td>{{ $x->created_at }}</td></tr>
      @empty
        <tr><td colspan="6">Нет данных</td></tr>
      @endforelse
    </tbody>
  </table>

  <h2>Счета (первые 50)</h2>
  <table>
    <thead><tr><th>ID</th><th>Клиент</th><th>Контракт</th><th>Номер</th><th>Дата</th><th>Срок</th><th>Валюта</th><th>Сумма</th><th>Статус</th><th>Создан</th></tr></thead>
    <tbody>
      @forelse($invoices as $x)
        <tr>
          <td>{{ $x->id }}</td>
          <td>{{ $x->client_id }}</td>
          <td>{{ $x->contract_id }}</td>
          <td>{{ $x->number }}</td>
          <td>{{ $x->issued_on }}</td>
          <td>{{ $x->due_on }}</td>
          <td>{{ $x->currency }}</td>
          <td>{{ $x->amount_cents }}</td>
          <td>{{ $x->status }}</td>
          <td>{{ $x->created_at }}</td>
        </tr>
      @empty
        <tr><td colspan="10">Нет данных</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
