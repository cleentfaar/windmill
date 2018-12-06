namespace Windmill.Game.Player
{
    public static class Factory
    {
        public static PlayerState Create(Type type)
        {
            return new PlayerState(
                Id.Generate(),
                type
            );
        }
    }
}