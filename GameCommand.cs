using System;
using ManyConsole;
using Windmill.Storage;
using Menu = EasyConsole.Menu;
using Factory = Windmill.Game.Factory;
using Id = Windmill.Game.Id;
using Type = Windmill.Game.Player.Type;

namespace Windmill
{
    public class GameCommand: ConsoleCommand
    {
        private Id _load;
        private string _pathToPgn;
        private readonly IAdapter _storage;

        public GameCommand()
        {
            IsCommand("game", "Start a new game or loads an existing one from the database or a PGN file");
            //HasAlias("--echo");

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

            //AllowsAnyAdditionalArguments("<foo1> <foo2> <fooN> where N is a word");
            
            _storage = new Postgres();
        }

        public override int Run(string[] remainingArguments)
        {
            if (_load is null)
            {
                if (string.IsNullOrWhiteSpace(_pathToPgn))
                {
                    Console.WriteLine("Starting new game");

                    _load = StartNewGame();
                }
                else
                {
                    Console.WriteLine("Importing game from PGN");

                    _load = ImportGame(_pathToPgn);
                }
            }
                
            Console.WriteLine("Loading game with ID "+_load);

            LoadGame(_load);

            return 0;
        }

        private void LoadGame(Id gameId)
        {
            //throw new NotImplementedException();
        }

        private Id ImportGame(string pgn)
        {
            throw new NotImplementedException();
        }

        private Id StartNewGame()
        {
            var id = Id.Generate();
            var mode = AskForMode();
            var color = AskForColor();
            var white = Game.Player.Factory.Create(mode < 3 ? Type.Human : Type.Computer);
            var black = Game.Player.Factory.Create(mode == 1 ? Type.Human : Type.Computer);
            var game = Factory.Create(
                id, 
                color == 1 ? white : black, 
                color == 2 ? black : white
            );

            _storage.Save(game);

            return id;
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
    }
}
