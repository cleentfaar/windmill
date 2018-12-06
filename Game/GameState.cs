using System.Collections.Generic;
using Windmill.Game.Player;

namespace Windmill.Game
{
    public class GameState
    {
        public GameState(
            Id id,
            PlayerState white,
            PlayerState black,
            Dictionary<int, int?> positions,
            List<string> history
        )
        {
        }
    }
}