## Windmill

Chess engine written in C#.

**UNDER CONSTRUCTION** (most components still need to be ported)

### Roadmap

- [x] Get familiar with writing console commands in C#.
- [ ] Port game-related classes from old project.
- [ ] Port rendering-related classes from old project.
- [ ] Find a way to make the rendering nicer by using a terminal's background and foreground colors.
- [ ] Adapt code to fit the [Universal Chess Interface](https://en.wikipedia.org/wiki/Universal_Chess_Interface).
- [ ] Implement multi-threading to solve positions in parallel.


### Requirements

- Docker + Docker-Compose

### Installation

1. Clone this repository.
`git clone git@github.com:cleentfaar/windmill`
2. Navigate to the cloned directory.
`cd windmill`

### Usage

For development purposes:
`make watch`

For production purposes (runs as analysis API, currently under construction)
`make run` 


### FAQ

Question | Answer
--- | ---
How do I play a game of chess with this code? | I'm still in the process of porting all code needed to just play a game. Further (interface-related) improvements will be needed to satisfy regular players.
