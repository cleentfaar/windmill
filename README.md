# Windmill

Windmill is a highly extensible chess framework written in PHP, powered by [Symfony](https://symfony.com).

**Notable features include**:
- play chess against any engine ([here](docs/supported_engines.md) is the list of supported engines so far), either through the CLI or web app (see Usage below).
- replay historic chess games from a given PGN file (see [datasets](./datasets) for an overview of built-in games)
- see the [Roadmap](#Roadmap) for more


## Requirements

- `go-task` (MacOS: `brew install go-task/tap/go-task`, otherwise see https://taskfile.dev/installation/)
- `docker` (see https://docs.docker.com/get-docker/)


## Usage (WIP)

- Run `make reset`
- To play a game of chess, run `make console COMMAND='bin/console windmill:play'` to start a game of chess. NOTE: You can use the `--engine=your_engine` option if you want to play against a non-random engine.
- To replay a game of chess, run `bin/console windmill:replay ./path/to/your/pgn_file.pgn` to replay a game from a PGN file, allowing you to go through it step by step.)


## Tests (WIP)

- Run `make tests` to run all tests


## Background/why

- I love chess.
- I've been a PHP programmer for 12+ years, turned Data Engineer for the last 2 years, and now want to see what I've been missing.
- I had some time to spare.


## Roadmap

- Preparation
  - [x] Dig into previous incarnations of this project to get inspired
  - [x] Get familiar with the last 2 years of Symfony (and PHP) improvements.
- Game fundamentals
  - [x] Create initial proof of concept
  - [x] Create a baseline of tests that the framework should abide to
  - [x] Implement algorithm that adheres to this baseline
  - [x] Be able to represent the game visually
    - CLI:
      - [x] Support FEN
      - [x] Support PGN
      - [x] Support SAN
      - [x] Support ASCII
    - Web App:
      - [ ] Provide the same functionality as the CLI, only now though a website
  - [ ] Create more complex scenarios, making sure the game logic deals with every possible case.
  - [ ] Add more engines besides 'Random'
  - [ ] Support persisting games so they can be continued (and/or analysed) later
