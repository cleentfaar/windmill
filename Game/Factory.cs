using System.Collections.Generic;
using Windmill.Game.Player;

namespace Windmill.Game
{
    public static class Factory
    {
        public static GameState Create(Id id, PlayerState white, PlayerState black)
        {
            return new GameState(
                id, 
                white, 
                black, 
                new Dictionary<int, int?>(), 
                new List<string>()
            );
        }
    }
}