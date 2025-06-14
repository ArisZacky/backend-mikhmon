# Dokumentasi API - Detail Transaksi

## Endpoint

```
POST http://127.0.0.1:8000/api/detail-transaksi
```

## Request Body (Required Fields)

Kirim data dalam format JSON dengan struktur berikut:

```json
{
    "transaksi_id": <integer>,
    "agent_id": <integer>,
    "details": [
        {
            "server": "<string>",
            "user": "<string>",
            "address": "<string>",
            "mac": "<string>",
            "uptime": "<string>",
            "bytes_in": "<string>",
            "bytes_out": "<string>",
            "time_left": "<string>",
            "login_by": "<string>",
            "comment": "<string>"
        }
    ]
}
```

## Contoh Request

```json
{
    "transaksi_id": 12345,
    "agent_id": 67890,
    "details": [
        {
            "server": "Server-1",
            "user": "user123",
            "address": "192.168.1.1",
            "mac": "00:1A:2B:3C:4D:5E",
            "uptime": "12h 30m",
            "bytes_in": "500MB",
            "bytes_out": "1GB",
            "time_left": "24h",
            "login_by": "admin",
            "comment": "User aktif sejak pagi"
        }
    ]
}
```

## Copy untuk Mencoba pada postman

```json
{
    "transaksi_id": ,
    "agent_id":,
    "details": [
        {
            "server": "",
            "user": "",
            "address": "",
            "mac": "",
            "uptime": "",
            "bytes_in": "",
            "bytes_out": "",
            "time_left": "",
            "login_by": "",
            "comment": ""
        }
    ]
}
```

## Response

Respon yang diterima akan berformat JSON sesuai hasil detail transaksi.

```json
{
    "status": "success",
    "message": "Detail transaksi ditemukan",
    "data": {
        "transaksi_id": 12345,
        "agent_id": 67890,
        "details": [
            {
                "server": "Server-1",
                "user": "user123",
                "address": "192.168.1.1",
                "mac": "00:1A:2B:3C:4D:5E",
                "uptime": "12h 30m",
                "bytes_in": "500MB",
                "bytes_out": "1GB",
                "time_left": "24h",
                "login_by": "admin",
                "comment": "active"
            }
        ]
    }
}
```

## Endpoint

```
GET http://127.0.0.1:8000/api/detail-transaksi
```

## Response Example (JSON):

```json
[
    {
        "id": 100,
        "transaksi_id": 94,
        "server": "hotspot1",
        "user": "idbc",
        "address": "10.9.1.253",
        "mac": "00:00:00:00:00:00",
        "uptime": "17s",
        "bytes_in": "0",
        "bytes_out": "0",
        "time_left": "0m",
        "login_by": "unknown",
        "comment": "up-345-03.19.25-2000",
        "created_at": "2025-03-19T07:29:49.000000Z",
        "updated_at": "2025-03-19T07:29:49.000000Z"
    },
    {
        "id": 101,
        "transaksi_id": 95,
        "server": "hotspot1",
        "user": "abc123rwnm",
        "address": "10.9.1.253",
        "mac": "00:00:00:00:00:00",
        "uptime": "25s",
        "bytes_in": "0",
        "bytes_out": "0",
        "time_left": "0m",
        "login_by": "unknown",
        "comment": "up-905-03.20.25-10000",
        "created_at": "2025-03-20T08:31:44.000000Z",
        "updated_at": "2025-03-20T08:31:44.000000Z"
    }
]
```

### Response Fields:

```json
id (int): ID detail transaksi.

transaksi_id (int): ID transaksi terkait.

server (string): Nama server.

user (string): Username pengguna.

address (string): Alamat IP pengguna.

mac (string): Alamat MAC pengguna.

uptime (string): Durasi koneksi pengguna.

bytes_in (string): Jumlah data masuk.

bytes_out (string): Jumlah data keluar.

time_left (string): Waktu tersisa untuk sesi.

login_by (string): Metode login.

comment (string): Komentar tambahan.

created_at (string): Timestamp detail transaksi dibuat.

updated_at (string): Timestamp detail transaksi diperbarui.
```

## Catatan

-   Pastikan semua field yang diwajibkan telah diisi.
-   Format data harus JSON.
-   Endpoint ini menerima metode `GET` & `POST`.  
    <br>

# Endpoint: Transactions

**_Pada Endpoint Transactions data table akan di generate langsung berdasarkan jumlah dan total voucher( detail transaksi)_**

**URL:** `http://127.0.0.1:8000/api/transactions`

**Method:** `GET`

### Response Example (JSON):

```json
[
    {
        "id": 94,
        "agent_id": 1,
        "jumlah": 1,
        "total_price": 2000,
        "created_at": "2025-03-19T07:29:49.000000Z",
        "updated_at": "2025-03-19T07:29:49.000000Z",
        "agent": {
            "id": 1,
            "nama": "agentPertama",
            "nomer_hp": "08123123",
            "email": "agentn@com.com",
            "created_at": "2025-03-18T01:24:54.000000Z",
            "updated_at": "2025-03-18T01:24:54.000000Z",
            "user_id": 1
        },
        "detail_transaksi": [
            {
                "id": 100,
                "transaksi_id": 94,
                "server": "hotspot1",
                "user": "idbc",
                "address": "10.9.1.253",
                "mac": "00:00:00:00:00:00",
                "uptime": "17s",
                "bytes_in": "0",
                "bytes_out": "0",
                "time_left": "0m",
                "login_by": "unknown",
                "comment": "up-345-03.19.25-2000",
                "created_at": "2025-03-19T07:29:49.000000Z",
                "updated_at": "2025-03-19T07:29:49.000000Z"
            }
        ]
    }
]
```

## ENDPOINT

```
http://127.0.0.1:8000/api/agents

Method: POST
```

## Request Body Required

```json
{
    "nama": <string>,
    "nomer_hp": <string>,
    "email": <string>
}
```

## Response Example

```json
{
    "message": "Agent berhasil ditambahkan",
    "agent": {
        "nama": "AgentNYaReseller",
        "nomer_hp": "10238123",
        "email": "oqiewh@fgnal.com",
        "user_id": 2,
        "updated_at": "2025-04-04T05:55:58.000000Z",
        "created_at": "2025-04-04T05:55:58.000000Z",
        "id": 2
    }
}
```

## ENDPOINT

```
http://127.0.0.1:8000/api/agents

Method: GET
```

## Response Example

```json
[
    {
        "id": 2,
        "nama": "AgentNYaReseller",
        "nomer_hp": "10238123",
        "email": "oqiewh@fgnal.com",
        "created_at": "2025-04-04T05:55:58.000000Z",
        "updated_at": "2025-04-04T05:55:58.000000Z",
        "user_id": 2
    }
]
```
