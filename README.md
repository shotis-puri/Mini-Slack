# Mini Slack

A tiny Slack-style chat app in plain PHP + SQLite. No framework, no Composer, no build step.

## Features

- Sign in with a username and password (an unknown username is registered automatically)
- Channels (`#general` and `#random` are created on first run; anyone can add more)
- Post and read messages; the page polls for new ones every 1.5 seconds

Not included: direct messages, threads, push updates, password reset.

## Requirements

- PHP 8.1+ with the `pdo_sqlite` extension (macOS: `brew install php`)

## Run

```sh
php -S localhost:8000 -t public
```

Open <http://localhost:8000>. The database file is created automatically in `data/` on first request.

## Project layout

```
public/            Web root (the only directory the server exposes)
  index.php        Login form and chat page
  api.php          JSON endpoint used by the chat page
  assets/app.js    Front-end: renders channels/messages, polls, sends
  assets/app.css   Styles
src/               Application code (not web-accessible)
  bootstrap.php    Starts the session and loads the modules below
  db.php           db(): SQLite connection, schema, default channels
  auth.php         currentUser(), loginOrRegister()
  channels.php     listChannels(), createChannel()
  messages.php     postMessage(), getMessages()
data/              SQLite database (app.sqlite); git-ignored
```

## How it works

**Storage.** `db()` opens `data/app.sqlite` and creates three tables if missing:
`users(id, name, pass)`, `channels(id, name)`, and
`messages(id, channel_id, user_id, body, created_at)`. Passwords are stored with `password_hash()`.

**Auth.** `index.php` posts the login form to itself. `loginOrRegister()` validates input, then either
verifies the password for an existing user or creates the user. The user id is kept in the PHP session
(`$_SESSION['uid']`). `?logout` destroys the session.

**API.** `public/api.php` returns JSON and responds `401` if you aren't signed in. It's selected by `?a=`:

| Action | Method | Params | Returns |
|---|---|---|---|
| `channels` | GET | – | `[{id, name}]` |
| `messages` | GET | `c` (channel id), `after` (last seen message id) | `[{id, user, body, ts}]` |
| `send` | POST | `c`, `body` | `{ok}` |
| `newchannel` | POST | `name` | `{id}` or `{error}` |

**Front-end.** `app.js` remembers the selected channel in `localStorage`, loads the channel list, and every
1.5 s asks for messages with `id > last`, appending only new ones. All user text is HTML-escaped before display.

## Notes

- This is a demo: there is no CSRF protection, rate limiting, or HTTPS. Don't expose it to the internet as-is.
- To reset everything, stop the server and delete `data/app.sqlite`.
