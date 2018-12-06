namespace Windmill.Game.Player
{
    public class PlayerState
    {
        private readonly Id Id;
        public readonly Type Type;

        public PlayerState(Id id, Type type)
        {
            Id = id;
            Type = type;
        }

        public override string ToString()
        {
            return Id.ToString();
        }
    }

    public enum Type
    {
        Human = 1,
        Computer = 2
    }
}