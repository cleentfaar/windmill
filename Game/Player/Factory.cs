namespace Windmill.Game.Player
{
    public static class Factory
    {
        public static Player Create(Type type)
        {
            return new Player(
                Id.Generate(),
                type
            );
        }
    }
}