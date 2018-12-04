using System.Collections.Generic;

namespace Windmill.Game
{
    public static class Factory
    {
        public static Game Create(Id id, Player.Player white, Player.Player black)
        {
            return new Game(
                id, 
                white, 
                black, 
                new Dictionary<int, int?>(), 
                new List<string>()
            );
        }
    }
}