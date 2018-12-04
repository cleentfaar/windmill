namespace Windmill.Game.Player
{
    public class Player
    {
        private readonly Id Id;
        private readonly Type Type;

        public Player(Id id, Type type)
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