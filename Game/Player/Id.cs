using System;

namespace Windmill.Game.Player
{
    public class Id
    {
        private readonly Guid _id;
        
        public Id(Guid id)
        {
            this._id = id;
        }

        public static Id Generate()
        {
            return new Id(System.Guid.NewGuid());
        }

        public override string ToString()
        {
            return _id.ToString();
        }

        public static Id FromString(string s)
        {
            return new Id(Guid.Parse(s));
        }
    }
}