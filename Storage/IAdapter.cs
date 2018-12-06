using Windmill.Game;

namespace Windmill.Storage
{
    public interface IAdapter
    {
        void Save(GameState gameState);
        GameState Load(Id id);
    }
}