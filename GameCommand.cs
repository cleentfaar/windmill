using System;
using ManyConsole;
using Windmill.Display;
using Windmill.Game;
using Windmill.Game.Player;
using PlayerType = Windmill.Game.Player.Type;
using PlayerFactory = Windmill.Game.Player.Factory;
using IAdapter = Windmill.Storage.IAdapter;
using Postgres = Windmill.Storage.Postgres;
using Factory = Windmill.Game.Factory;
using Id = Windmill.Game.Id;
using Menu = EasyConsoleCore.Menu;

namespace Windmill
{
    public class GameCommand: ConsoleCommand
    {
        private Id _load;
        private string _pathToPgn;
        private readonly IAdapter _storage;

        public GameCommand()
        {
            IsCommand("game", "Start a new game or loads an existing game from the database or a PGN file");

            HasOption(
                "l|load=",
                "The ID of a game to load from the database.",
                v => _load = Id.FromString(v)
            );

            HasOption(
                "p|pgn=",
                "The path to a PGN file.",
                v => _pathToPgn = v
            );

            // TODO Implement DI for this stuff
            _storage = new Postgres();
        }

        public override int Run(string[] remainingArguments)
        {
            GameState gameState = null;
            
            if (_load is null)
            {
                if (string.IsNullOrWhiteSpace(_pathToPgn))
                {
                    gameState = StartNewGame();
                }
                else
                {
                    gameState = ImportGame(_pathToPgn);
                }

                //SaveGame(game);
            } else {
                gameState = LoadGame(_load);
            }

            RenderGame(gameState);

            var nextMove = AskForNextMove();
            
            

            return 0;
        }

        private void RenderGame(GameState gameState)
        {
            AsciiGameRenderer renderer = new AsciiGameRenderer();

            string ascii = renderer.Render(gameState);
            
            Console.WriteLine(ascii);
        }

        private void SaveGame(GameState gameState)
        {
            _storage.Save(gameState);
        }

        private GameState LoadGame(Id gameId)
        {
            Console.WriteLine("Loading game with ID "+_load);
            
            return _storage.Load(gameId);
        }

        private GameState ImportGame(string pgn)
        {
            Console.WriteLine("Importing game from PGN");

            throw new NotImplementedException();
        }

        private GameState StartNewGame()
        {
            Console.WriteLine("Starting new game");

            Id id = Id.Generate();
            int mode = AskForMode();
            int color = AskForColor();
            PlayerState white = PlayerFactory.Create(mode < 3 ? PlayerType.Human : PlayerType.Computer);
            PlayerState black = PlayerFactory.Create(mode == 1 ? PlayerType.Human : PlayerType.Computer);
            GameState gameState = Factory.Create(
                id, 
                color == 1 ? white : black, 
                color == 2 ? black : white
            );

            return gameState;
        }

        private int AskForMode()
        {
            int value = 0;

            Console.WriteLine("Who should play against eachother?");

            var modeSelection = new Menu()
                .Add("Human vs Human", () => value = 1)
                .Add("Human vs Computer", () => value = 2)
                .Add("Computer vs Computer", () => value = 3)
            ;

            modeSelection.Display();
            
            return value;
        }

        private int AskForColor()
        {
            int value = 0;

            Console.WriteLine("Would you like to play as white or black?");

            var modeSelection = new Menu()
                .Add("White", () => value = 1)
                .Add("Black", () => value = 2);

            modeSelection.Display();
            
            return value;
        }

        private string AskForNextMove()
        {
            Console.WriteLine("What move would you like to make?");

            var value = Console.ReadLine();
            
            return value;
        }
    }
}
